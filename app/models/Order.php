<?php

require_once 'BaseModel.php';

class Order extends BaseModel {
    protected $table = 'pedidos';
    
    public function createOrderWithItems($orderData, $items) {
        try {
            $this->db->beginTransaction();
            
            // Criar pedido
            $orderId = $this->create($orderData);
            
            if ($orderId && !empty($items)) {
                // Criar itens do pedido
                $stmt = $this->db->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco, variacao_id) VALUES (?, ?, ?, ?, ?)");
                
                foreach ($items as $item) {
                    $stmt->execute([
                        $orderId,
                        $item['produto_id'],
                        $item['quantidade'],
                        $item['preco'],
                        $item['variacao_id'] ?? null
                    ]);
                    
                    // Diminuir estoque
                    $stockModel = new Stock();
                    $stockModel->decreaseStock($item['produto_id'], $item['variacao_id'], $item['quantidade']);
                }
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getOrderWithItems($orderId) {
        // Buscar pedido
        $order = $this->findById($orderId);
        if (!$order) {
            return null;
        }
        
        // Buscar itens do pedido
        $sql = "SELECT pi.*, p.nome as produto_nome 
                FROM pedido_itens pi 
                JOIN produtos p ON pi.produto_id = p.id 
                WHERE pi.pedido_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);
        $order['itens'] = $stmt->fetchAll();
        
        return $order;
    }
    
    public function updateOrderStatus($orderId, $status) {
        return $this->update($orderId, ['status' => $status]);
    }
    
    public function cancelOrder($orderId) {
        try {
            $this->db->beginTransaction();
            
            // Buscar itens do pedido para devolver ao estoque
            $order = $this->getOrderWithItems($orderId);
            
            if ($order) {
                // Devolver itens ao estoque
                $stockModel = new Stock();
                foreach ($order['itens'] as $item) {
                    $stockModel->increaseStock($item['produto_id'], $item['variacao_id'], $item['quantidade']);
                }
                
                // Atualizar status do pedido
                $this->updateOrderStatus($orderId, 'cancelado');
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getOrdersByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE status = ? ORDER BY created_at DESC");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    public function getOrdersByDateRange($startDate, $endDate) {
        $stmt = $this->db->prepare("SELECT * FROM pedidos WHERE DATE(created_at) BETWEEN ? AND ? ORDER BY created_at DESC");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }
    
    public function calculateShipping($subtotal) {
        if ($subtotal >= 200.00) {
            return 0.00; // Frete grátis
        } elseif ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } else {
            return 20.00;
        }
    }
    
    public function getOrderStats() {
        $sql = "SELECT 
                    COUNT(*) as total_pedidos,
                    SUM(CASE WHEN status = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                    SUM(CASE WHEN status = 'processando' THEN 1 ELSE 0 END) as processando,
                    SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluidos,
                    SUM(CASE WHEN status = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                    SUM(total) as valor_total
                FROM pedidos";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllOrdersWithSummary() {
        $sql = "SELECT 
                p.id,
                p.subtotal,
                p.frete,
                p.total,
                p.status,
                p.cep,
                p.endereco,
                p.email,
                p.cupom_codigo,
                p.desconto,
                p.created_at,
                p.updated_at,
                COUNT(pi.id) AS total_itens
            FROM pedidos p
            LEFT JOIN pedido_itens pi ON pi.pedido_id = p.id
            GROUP BY p.id
            ORDER BY p.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

