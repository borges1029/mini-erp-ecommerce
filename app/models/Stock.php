<?php

require_once 'BaseModel.php';

class Stock extends BaseModel {
    protected $table = 'estoque';
    
    public function getStockByProduct($productId) {
        $stmt = $this->db->prepare("SELECT * FROM estoque WHERE produto_id = ? ORDER BY variacao_id");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }
    
    public function getStockByProductAndVariation($productId, $variationId) {
        $stmt = $this->db->prepare("SELECT * FROM estoque WHERE produto_id = ? AND variacao_id = ?");
        $stmt->execute([$productId, $variationId]);
        return $stmt->fetch();
    }
    
    public function updateStock($productId, $variationId, $quantity) {
        $stmt = $this->db->prepare("UPDATE estoque SET quantidade = ? WHERE produto_id = ? AND variacao_id = ?");
        return $stmt->execute([$quantity, $productId, $variationId]);
    }
    
    public function decreaseStock($productId, $variationId, $quantity) {
        // Verificar se há estoque suficiente
        $currentStock = $this->getStockByProductAndVariation($productId, $variationId);
        
        if (!$currentStock || $currentStock['quantidade'] < $quantity) {
            return false; // Estoque insuficiente
        }
        
        $newQuantity = $currentStock['quantidade'] - $quantity;
        return $this->updateStock($productId, $variationId, $newQuantity);
    }
    
    public function increaseStock($productId, $variationId, $quantity) {
        $currentStock = $this->getStockByProductAndVariation($productId, $variationId);
        
        if (!$currentStock) {
            // Criar novo registro de estoque
            return $this->create([
                'produto_id' => $productId,
                'variacao_id' => $variationId,
                'quantidade' => $quantity
            ]);
        }
        
        $newQuantity = $currentStock['quantidade'] + $quantity;
        return $this->updateStock($productId, $variationId, $newQuantity);
    }
    
    public function checkAvailability($productId, $variationId, $requiredQuantity) {
        $stock = $this->getStockByProductAndVariation($productId, $variationId);
        
        if (!$stock) {
            return false;
        }
        
        return $stock['quantidade'] >= $requiredQuantity;
    }
    
    public function getLowStockItems($threshold = 5) {
        $sql = "SELECT e.*, p.nome as produto_nome 
                FROM estoque e 
                JOIN produtos p ON e.produto_id = p.id 
                WHERE e.quantidade <= ? 
                ORDER BY e.quantidade ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }
    
    public function getTotalStockByProduct($productId) {
        $stmt = $this->db->prepare("SELECT SUM(quantidade) as total FROM estoque WHERE produto_id = ?");
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}

