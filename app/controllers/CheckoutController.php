<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Stock.php';
require_once __DIR__ . '/../models/Coupon.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CheckoutController extends BaseController {
    private $orderModel;
    private $productModel;
    private $stockModel;
    private $couponModel;
    
    public function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->stockModel = new Stock();
        $this->couponModel = new Coupon();
    }
    
    public function index() {
        // Verificar se há itens no carrinho
        if (empty($_SESSION['cart']['items'])) {
            $this->setFlashMessage('warning', 'Seu carrinho está vazio');
            $this->redirect(BASE_URL . '/?p=cart&a=viewCart');
        }
        
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        $shipping = $this->calculateShipping($subtotal);
        $discount = $this->getCartDiscount();
        $total = $subtotal + $shipping - $discount;
        
        if ($this->isPost()) {
            $this->processCheckout();
        } else {
            $this->view('checkout/index', [
                'title' => 'Finalizar Compra',
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'discount' => $discount,
                'total' => $total,
                'appliedCoupon' => $_SESSION['cart']['coupon'] ?? null
            ]);
        }
    }
    
    public function success() {
        $orderId = $this->getParam('order_id');
        
        if (!$orderId) {
            $this->redirect(BASE_URL);
        }
        
        $order = $this->orderModel->getOrderWithItems($orderId);
        
        if (!$order) {
            $this->setFlashMessage('error', 'Pedido não encontrado');
            $this->redirect(BASE_URL);
        }
        
        $this->view('checkout/success', [
            'title' => 'Pedido Realizado com Sucesso',
            'order' => $order
        ]);
    }
    
    public function validateCep() {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
        }
        
        $data = $this->getPostData();
        $cep = preg_replace('/\D/', '', $data['cep'] ?? '');
        
        if (strlen($cep) !== 8) {
            $this->json(['success' => false, 'message' => 'CEP deve ter 8 dígitos']);
        }
        
        // Integração com ViaCEP
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        $response = file_get_contents($url);
        
        if ($response === false) {
            $this->json(['success' => false, 'message' => 'Erro ao consultar CEP']);
        }
        
        $data = json_decode($response, true);
        
        if (isset($data['erro'])) {
            $this->json(['success' => false, 'message' => 'CEP não encontrado']);
        }

        $this->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
    private function processCheckout() {
        $data = $this->sanitizeInput($this->getPostData());
        
        // Validar dados obrigatórios
        $errors = $this->validateRequired($data, ['cep', 'endereco', 'email']);
        
        if (!empty($errors)) {
            $this->setFlashMessage('error', implode('<br>', $errors));
            return;
        }
        
        // Validar formato do email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->setFlashMessage('error', 'Email inválido');
            return;
        }
        
        // Validar CEP
        $cep = preg_replace('/\D/', '', $data['cep']);
        if (strlen($cep) !== 8) {
            $this->setFlashMessage('error', 'CEP deve ter 8 dígitos');
            return;
        }
        
        try {
            // Obter dados do carrinho
            $cartItems = $this->getCartItems();
            $subtotal = $this->calculateSubtotal($cartItems);
            $shipping = $this->calculateShipping($subtotal);
            $discount = $this->getCartDiscount();
            $total = $subtotal + $shipping - $discount;
            
            // Verificar estoque novamente
            foreach ($cartItems as $item) {
                $available = $this->stockModel->checkAvailability(
                    $item['product']['id'], 
                    $item['variation_id'], 
                    $item['quantity']
                );
                
                if (!$available) {
                    $this->setFlashMessage('error', 'Estoque insuficiente para: ' . $item['product']['nome']);
                    return;
                }
            }
            
            // Preparar dados do pedido
            $orderData = [
                'subtotal' => $subtotal,
                'frete' => $shipping,
                'total' => $total,
                'status' => 'pendente',
                'cep' => $cep,
                'endereco' => $data['endereco'],
                'email' => $data['email'],
                'cupom_codigo' => isset($_SESSION['cart']['coupon']) ? $_SESSION['cart']['coupon']['codigo'] : null,
                'desconto' => $discount
            ];
            
            // Preparar itens do pedido
            $orderItems = [];
            foreach ($cartItems as $item) {
                $orderItems[] = [
                    'produto_id' => $item['product']['id'],
                    'quantidade' => $item['quantity'],
                    'preco' => $item['product']['preco'],
                    'variacao_id' => $item['variation_id']
                ];
            }
            
            // Criar pedido
            $orderId = $this->orderModel->createOrderWithItems($orderData, $orderItems);
            
            if ($orderId) {
                // Limpar carrinho
                $_SESSION['cart'] = ['items' => [], 'coupon' => null];
                
                // Enviar email (implementar depois)
                $this->sendOrderEmail($orderId, $data['email']);
                
                $this->setFlashMessage('success', 'Pedido realizado com sucesso!');
                $this->redirect(BASE_URL . '/?p=checkout&a=success&order_id=' . $orderId);
            } else {
                $this->setFlashMessage('error', 'Erro ao processar pedido');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Erro ao processar pedido: ' . $e->getMessage());
        }
    }

    public function resendEmail()
    {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
        }

        $data = $this->getPostData();
        $orderId = $data['order_id'];

        if ($orderId) {
            // Recupera os dados do pedido
            $order = $this->orderModel->getOrderWithItems($orderId);
            $email = $order['email'];

            //Faz o envio
            $response = $this->sendOrderEmail($orderId, $email, $order);
        } else {
            $response = ['success' => false, 'message' => 'Pedido não encontrado'];
        }

        $this->json($response);
    }

    private function sendOrderEmail($orderId, $email, $order) {
        $subject = "Pedido #{$orderId} - Mini ERP";
        $message = "Seu pedido foi recebido com sucesso!\n\n";
        $message .= "Número do pedido: #{$orderId}\n";
        $message .= "Total: R$ " . number_format($order['total'], 2, ',', '.') . "\n";
        $message .= "Status: " . ucfirst($order['status']) . "\n\n";
        $message .= "Obrigado por sua compra!";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();

            // Pega host e porta da constante
            $host = EMAIL_SERVER;
            $port = defined('EMAIL_PORT') && EMAIL_PORT ? EMAIL_PORT : 587;

            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_USERNAME;
            $mail->Password   = EMAIL_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ou PHPMailer::ENCRYPTION_SMTPS
            $mail->Port       = $port;

            $mail->setFrom(FROM_EMAIL, SITE_NAME);
            $mail->addAddress($email);

            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();

            $result = ['success' => true, 'message' => "Email enviado para {$email}: {$subject}"];
        } catch (Exception $e) {
            $result = ['success' => false, 'message' => "Erro ao enviar email: {$mail->ErrorInfo}"];
        }

        return $result;
    }


    // Métodos auxiliares (copiados do CartController)
    private function getCartItems() {
        $items = [];
        
        if (!isset($_SESSION['cart']['items'])) {
            return $items;
        }
        
        foreach ($_SESSION['cart']['items'] as $cartItem) {
            $product = $this->productModel->findById($cartItem['product_id']);
            if ($product) {
                $items[] = [
                    'product' => $product,
                    'variation_id' => $cartItem['variation_id'],
                    'quantity' => $cartItem['quantity'],
                    'subtotal' => $product['preco'] * $cartItem['quantity']
                ];
            }
        }
        
        return $items;
    }
    
    private function calculateSubtotal($cartItems) {
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['subtotal'];
        }
        return $subtotal;
    }
    
    private function calculateShipping($subtotal) {
        if ($subtotal >= 200.00) {
            return 0.00; // Frete grátis
        } elseif ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
    
    private function getCartDiscount() {
        if (!isset($_SESSION['cart']['coupon'])) {
            return 0.00;
        }
        
        $coupon = $_SESSION['cart']['coupon'];
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        
        $validation = $this->couponModel->validateCoupon($coupon['codigo'], $subtotal);
        
        if ($validation['valid']) {
            return $validation['discount'];
        }
        
        // Remove invalid coupon
        $_SESSION['cart']['coupon'] = null;
        return 0.00;
    }
}

