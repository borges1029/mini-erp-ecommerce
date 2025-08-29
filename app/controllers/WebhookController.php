<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Order.php';

class WebhookController extends BaseController {
    private $orderModel;
    
    public function __construct() {
        $this->orderModel = new Order();
    }
    
    public function orderStatus() {
        // Verificar se é uma requisição POST
        if (!$this->isPost()) {
            http_response_code(405);
            $this->json(['error' => 'Method not allowed']);
        }
        
        // Obter dados do webhook
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        // Log do webhook recebido
        $this->logWebhook('order_status', $data);
        
        // Validar dados obrigatórios
        if (!isset($data['order_id']) || !isset($data['status'])) {
            http_response_code(400);
            $this->json(['error' => 'Missing required fields: order_id, status']);
        }
        
        $orderId = intval($data['order_id']);
        $status = trim(strtolower($data['status']));
        
        // Verificar se o pedido existe
        $order = $this->orderModel->findById($orderId);
        if (!$order) {
            http_response_code(404);
            $this->json(['error' => 'Order not found']);
        }
        
        try {
            // Processar status
            switch ($status) {
                case 'cancelado':
                case 'cancelled':
                    $this->handleCancelledOrder($orderId);
                    break;
                    
                case 'confirmado':
                case 'confirmed':
                    $this->handleConfirmedOrder($orderId);
                    break;
                    
                case 'enviado':
                case 'shipped':
                    $this->handleShippedOrder($orderId);
                    break;
                    
                case 'entregue':
                case 'delivered':
                    $this->handleDeliveredOrder($orderId);
                    break;
                    
                default:
                    // Atualizar status genérico
                    $this->orderModel->updateOrderStatus($orderId, $status);
                    break;
            }
            
            // Resposta de sucesso
            $this->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order_id' => $orderId,
                'new_status' => $status
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            $this->json(['error' => 'Internal server error: ' . $e->getMessage()]);
        }
    }
    
    public function test() {
        // Endpoint para testar o webhook
        $this->json([
            'success' => true,
            'message' => 'Webhook endpoint is working',
            'timestamp' => date('Y-m-d H:i:s'),
            'server_info' => [
                'method' => $_SERVER['REQUEST_METHOD'],
                'content_type' => $_SERVER['CONTENT_TYPE'] ?? 'not set',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'not set'
            ]
        ]);
    }
    
    private function handleCancelledOrder($orderId) {
        // Cancelar pedido e restaurar estoque
        $order = $this->orderModel->getOrderWithItems($orderId);
        
        if ($order && $order['status'] !== 'cancelado') {
            // Restaurar estoque
            foreach ($order['itens'] as $item) {
                $this->restoreStock($item['produto_id'], $item['variacao_id'], $item['quantidade']);
            }
            
            // Atualizar status
            $this->orderModel->updateOrderStatus($orderId, 'cancelado');
            
            // Enviar email de cancelamento
            $this->sendCancellationEmail($order);
            
            $this->logWebhook('order_cancelled', [
                'order_id' => $orderId,
                'stock_restored' => true,
                'email_sent' => true
            ]);
        }
    }
    
    private function handleConfirmedOrder($orderId) {
        $this->orderModel->updateOrderStatus($orderId, 'confirmado');
        
        $order = $this->orderModel->findById($orderId);
        $this->sendConfirmationEmail($order);
        
        $this->logWebhook('order_confirmed', [
            'order_id' => $orderId,
            'email_sent' => true
        ]);
    }
    
    private function handleShippedOrder($orderId) {
        $this->orderModel->updateOrderStatus($orderId, 'enviado');
        
        $order = $this->orderModel->findById($orderId);
        $this->sendShippingEmail($order);
        
        $this->logWebhook('order_shipped', [
            'order_id' => $orderId,
            'email_sent' => true
        ]);
    }
    
    private function handleDeliveredOrder($orderId) {
        $this->orderModel->updateOrderStatus($orderId, 'entregue');
        
        $order = $this->orderModel->findById($orderId);
        $this->sendDeliveryEmail($order);
        
        $this->logWebhook('order_delivered', [
            'order_id' => $orderId,
            'email_sent' => true
        ]);
    }
    
    private function restoreStock($productId, $variationId, $quantity) {
        // Implementar lógica para restaurar estoque
        // Por simplicidade, vamos apenas logar a ação
        $this->logWebhook('stock_restored', [
            'product_id' => $productId,
            'variation_id' => $variationId,
            'quantity' => $quantity
        ]);
        
        // Em uma implementação real, você restauraria o estoque aqui
        // $this->stockModel->addStock($productId, $variationId, $quantity);
    }
    
    private function sendCancellationEmail($order) {
        $subject = "Pedido #{$order['id']} Cancelado - Mini ERP";
        $message = "Seu pedido foi cancelado.\n\n";
        $message .= "Número do pedido: #{$order['id']}\n";
        $message .= "Valor: R$ " . number_format($order['total'], 2, ',', '.') . "\n\n";
        $message .= "Se você não solicitou o cancelamento, entre em contato conosco.";
        
        $this->sendEmail($order['email'], $subject, $message);
    }
    
    private function sendConfirmationEmail($order) {
        $subject = "Pedido #{$order['id']} Confirmado - Mini ERP";
        $message = "Seu pedido foi confirmado e está sendo processado.\n\n";
        $message .= "Número do pedido: #{$order['id']}\n";
        $message .= "Status: Confirmado\n\n";
        $message .= "Você receberá uma nova notificação quando o pedido for enviado.";
        
        $this->sendEmail($order['email'], $subject, $message);
    }
    
    private function sendShippingEmail($order) {
        $subject = "Pedido #{$order['id']} Enviado - Mini ERP";
        $message = "Seu pedido foi enviado!\n\n";
        $message .= "Número do pedido: #{$order['id']}\n";
        $message .= "Status: Enviado\n\n";
        $message .= "Você receberá o código de rastreamento em breve.";
        
        $this->sendEmail($order['email'], $subject, $message);
    }
    
    private function sendDeliveryEmail($order) {
        $subject = "Pedido #{$order['id']} Entregue - Mini ERP";
        $message = "Seu pedido foi entregue com sucesso!\n\n";
        $message .= "Número do pedido: #{$order['id']}\n";
        $message .= "Status: Entregue\n\n";
        $message .= "Obrigado por comprar conosco!";
        
        $this->sendEmail($order['email'], $subject, $message);
    }
    
    private function sendEmail($to, $subject, $message) {
        // Implementação básica de envio de email
        // Em produção, usar uma biblioteca como PHPMailer
        
        $headers = "From: noreply@mini-erp.com\r\n";
        $headers .= "Reply-To: noreply@mini-erp.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        // Em ambiente de desenvolvimento, apenas log
        error_log("Email enviado para {$to}: {$subject}");
        
        // mail($to, $subject, $message, $headers);
    }
    
    private function logWebhook($event, $data) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'data' => $data,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        // Log para arquivo
        $logFile = __DIR__ . '/../../logs/webhooks.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
}

