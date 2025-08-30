<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Stock.php';
require_once __DIR__ . '/../models/Coupon.php';

class  CartController extends BaseController {
    private $productModel;
    private $stockModel;
    private $couponModel;
    
    public function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $this->productModel = new Product();
        $this->stockModel = new Stock();
        $this->couponModel = new Coupon();
    }
    
    public function add() {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
        }
        
        $data = $this->getPostData();
        $productId = intval($data['product_id'] ?? 0);
        $variationId = $data['variation_id'] ?? '';
        $quantity = intval($data['quantity'] ?? 1);
        
        if (!$productId || $quantity <= 0) {
            $this->json(['success' => false, 'message' => 'Dados inválidos']);
        }
        
        // Verificar se o produto existe
        $product = $this->productModel->findById($productId);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Produto não encontrado']);
        }
        
        // Verificar estoque
        if ($variationId) {
            $available = $this->stockModel->checkAvailability($productId, $variationId, $quantity);
        } else {
            $totalStock = $this->stockModel->getTotalStockByProduct($productId);
            $available = $totalStock >= $quantity;
        }
        
        if (!$available) {
            $this->json(['success' => false, 'message' => 'Estoque insuficiente']);
        }
        
        // Adicionar ao carrinho
        $this->addToCart($productId, $variationId, $quantity);
        
        $this->json(['success' => true, 'message' => 'Produto adicionado ao carrinho']);
    }
    
    public function viewCart() {
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        $shipping = $this->calculateShipping($subtotal);
        $discount = $this->getCartDiscount();
        $total = $subtotal + $shipping - $discount;
        
        $this->view('cart/view', [
            'title' => 'Carrinho de Compras',
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'appliedCoupon' => $_SESSION['cart']['coupon'] ?? null
        ]);
    }
    
    public function update() {
        if (!$this->isPost()) {
            $this->redirect(BASE_URL . '/?p=cart&a=viewCart');
        }
        
        $data = $this->getPostData();
        $productId = intval($data['product_id'] ?? 0);
        $variationId = $data['variation_id'] ?? '';
        $quantity = intval($data['quantity'] ?? 0);

        if ($quantity <= 0) {
            $this->removeFromCart($productId, $variationId);
            $this->json(['success' => true, 'message' => 'Produto removido do carrinho']);
            return;
        }

        // Verificar estoque antes de atualizar
        if ($variationId) {
            $available = $this->stockModel->checkAvailability($productId, $variationId, $quantity);
        } else {
            $totalStock = $this->stockModel->getTotalStockByProduct($productId);
            $available = $totalStock >= $quantity;
        }

        if (!$available) {
            $this->json(['success' => false, 'message' => 'Estoque insuficiente para a quantidade solicitada']);
            return;
        }

        $this->updateCartItem($productId, $variationId, $quantity);
        $this->json(['success' => true, 'message' => 'Carrinho atualizado']);
    }
    
    public function remove() {
        $productId = intval($this->getParam('product_id', 0));
        $variationId = $this->getParam('variation_id', '');
        
        $this->removeFromCart($productId, $variationId);
        
        if ($this->isPost()) {
            $this->json(['success' => true, 'message' => 'Item removido do carrinho']);
        } else {
            $this->setFlashMessage('success', 'Item removido do carrinho');
            $this->redirect(BASE_URL . '/?p=cart&a=viewCart');
        }
    }
    
    public function clear() {
        $_SESSION['cart'] = ['items' => [], 'coupon' => null];
        
        if ($this->isPost()) {
            $this->json(['success' => true, 'message' => 'Carrinho limpo']);
        } else {
            $this->setFlashMessage('success', 'Carrinho limpo');
            $this->redirect(BASE_URL . '/?p=cart&a=viewCart');
        }
    }
    
    public function count() {
        $count = 0;
        if (isset($_SESSION['cart']['items'])) {
            foreach ($_SESSION['cart']['items'] as $item) {
                $count += $item['quantity'];
            }
        }
        
        $this->json(['count' => $count]);
    }
    
    public function applyCoupon() {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
        }
        
        $data = $this->getPostData();
        $couponCode = trim($data['coupon_code'] ?? '');
        
        if (!$couponCode) {
            $this->json(['success' => false, 'message' => 'Código do cupom é obrigatório']);
        }
        
        $cartItems = $this->getCartItems();
        $subtotal = $this->calculateSubtotal($cartItems);
        
        $validation = $this->couponModel->validateCoupon($couponCode, $subtotal);
        
        if ($validation['valid']) {
            $_SESSION['cart']['coupon'] = $validation['coupon'];
            $this->json(['success' => true, 'message' => $validation['message']]);
        } else {
            $this->json(['success' => false, 'message' => $validation['message']]);
        }
    }
    
    public function removeCoupon() {
        $_SESSION['cart']['coupon'] = null;
        
        if ($this->isPost()) {
            $this->json(['success' => true, 'message' => 'Cupom removido']);
        } else {
            $this->setFlashMessage('success', 'Cupom removido');
            $this->redirect(BASE_URL . '/?p=cart&a=viewCart');
        }
    }
    
    // Private methods
    private function addToCart($productId, $variationId, $quantity) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = ['items' => [], 'coupon' => null];
        }
        
        $key = $productId . '_' . $variationId;
        
        if (isset($_SESSION['cart']['items'][$key])) {
            $_SESSION['cart']['items'][$key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart']['items'][$key] = [
                'product_id' => $productId,
                'variation_id' => $variationId,
                'quantity' => $quantity
            ];
        }
    }
    
    private function updateCartItem($productId, $variationId, $quantity) {
        $key = $productId . '_' . $variationId;
        
        if (isset($_SESSION['cart']['items'][$key])) {
            $_SESSION['cart']['items'][$key]['quantity'] = $quantity;
        }
    }
    
    private function removeFromCart($productId, $variationId) {
        $key = $productId . '_' . $variationId;
        
        if (isset($_SESSION['cart']['items'][$key])) {
            unset($_SESSION['cart']['items'][$key]);
        }
    }
    
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

