<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController extends BaseController {
    private $orderModel;
    
    public function __construct() {
        $this->orderModel = new Order();
    }
    
    public function index() {
        $orders = $this->orderModel->getAllOrdersWithSummary();
        $stats = $this->orderModel->getOrderStats();
        
        $this->view('orders/index', [
            'title' => 'Gerenciar Pedidos',
            'orders' => $orders,
            'stats' => $stats
        ]);
    }
    
    public function viewOrder() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->setFlashMessage('error', 'ID do pedido não informado');
            $this->redirect(BASE_URL . '/?p=order&a=index');
        }
        
        $order = $this->orderModel->getOrderWithItems($id);
        
        if (!$order) {
            $this->setFlashMessage('error', 'Pedido não encontrado');
            $this->redirect(BASE_URL . '/?p=order&a=index');
        }
        
        $this->view('orders/view', [
            'title' => 'Pedido #' . $order['id'],
            'order' => $order
        ]);
    }
    
    public function updateStatus() {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
        }
        
        $data = $this->getPostData();
        $orderId = intval($data['order_id'] ?? 0);
        $status = trim($data['status'] ?? '');
        
        if (!$orderId || !$status) {
            $this->json(['success' => false, 'message' => 'Dados inválidos']);
        }
        
        try {
            $this->orderModel->updateOrderStatus($orderId, $status);
            $this->json(['success' => true, 'message' => 'Status atualizado com sucesso']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erro ao atualizar status: ' . $e->getMessage()]);
        }
    }
    
    public function delete() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID não informado']);
        }
        
        try {
            $this->orderModel->delete($id);
            $this->json(['success' => true, 'message' => 'Pedido excluído com sucesso']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erro ao excluir pedido: ' . $e->getMessage()]);
        }
    }
    
    public function export() {
        $format = $this->getParam('format', 'csv');
        $orders = $this->orderModel->getAllOrdersWithSummary();
        
        if ($format === 'csv') {
            $this->exportCSV($orders);
        } else {
            $this->setFlashMessage('error', 'Formato não suportado');
            $this->redirect(BASE_URL . '/?p=order&a=index');
        }
    }
    
    private function exportCSV($orders) {
        $filename = 'pedidos_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Cabeçalho
        fputcsv($output, [
            'ID',
            'Data',
            'Email',
            'Status',
            'Subtotal',
            'Frete',
            'Desconto',
            'Total',
            'CEP',
            'Endereço',
            'Cupom'
        ], ';');
        
        // Dados
        foreach ($orders as $order) {
            fputcsv($output, [
                $order['id'],
                date('d/m/Y H:i', strtotime($order['created_at'])),
                $order['email'],
                ucfirst($order['status']),
                number_format($order['subtotal'], 2, ',', '.'),
                number_format($order['frete'], 2, ',', '.'),
                number_format($order['desconto'], 2, ',', '.'),
                number_format($order['total'], 2, ',', '.'),
                $order['cep'],
                $order['endereco'],
                $order['cupom_codigo'] ?? ''
            ], ';');
        }
        
        fclose($output);
        exit();
    }
}

