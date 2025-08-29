<?php

require_once 'BaseModel.php';

class Product extends BaseModel {
    protected $table = 'produtos';
    
    public function getProductsWithStock() {
        $sql = "SELECT p.*, 
                       SUM(e.quantidade) as estoque_total,
                       COUNT(e.id) as variacoes_count
                FROM produtos p 
                LEFT JOIN estoque e ON p.id = e.produto_id 
                GROUP BY p.id 
                ORDER BY p.id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getProductWithVariations($id) {
        // Buscar produto
        $product = $this->findById($id);
        if (!$product) {
            return null;
        }
        
        // Buscar variações com estoque
        $stmt = $this->db->prepare("SELECT * FROM estoque WHERE produto_id = ? ORDER BY variacao_id");
        $stmt->execute([$id]);
        $variations = $stmt->fetchAll();
        
        $product['variacoes_estoque'] = $variations;
        
        // Decodificar JSON das variações
        if ($product['variacoes']) {
            $product['variacoes'] = json_decode($product['variacoes'], true);
        }
        
        return $product;
    }
    
    public function createWithVariations($productData, $variationsStock) {
        try {
            $this->db->beginTransaction();
            
            // Criar produto
            $productId = $this->create($productData);
            
            if ($productId && !empty($variationsStock)) {
                // Criar registros de estoque para cada variação
                $stockModel = new Stock();
                foreach ($variationsStock as $variation) {
                    $variation['produto_id'] = $productId;
                    $stockModel->create($variation);
                }
            }
            
            $this->db->commit();
            return $productId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function updateWithVariations($id, $productData, $variationsStock) {
        try {
            $this->db->beginTransaction();

            // Atualizar produto
            $this->update($id, $productData);

            // Remover estoque antigo
            $stmt = $this->db->prepare("DELETE FROM estoque WHERE produto_id = ?");
            $stmt->execute([$id]);

            // Criar novos registros de estoque
            if (!empty($variationsStock)) {
                $stockModel = new Stock();
                foreach ($variationsStock as $variation) {
                    $variation['produto_id'] = $id;
                    $stockModel->create($variation);
                }
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function searchProducts($term) {
        $stmt = $this->db->prepare("SELECT * FROM produtos WHERE nome LIKE ? ORDER BY nome");
        $stmt->execute(["%{$term}%"]);
        return $stmt->fetchAll();
    }
}

