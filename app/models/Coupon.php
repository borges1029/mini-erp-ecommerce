<?php

require_once 'BaseModel.php';

class Coupon extends BaseModel {
    protected $table = 'cupons';
    
    public function validateCoupon($code, $subtotal) {
        $stmt = $this->db->prepare("SELECT * FROM cupons WHERE codigo = ? AND ativo = 1 AND validade >= CURDATE()");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();
        
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Cupom inválido ou expirado'];
        }
        
        // Verificar valor mínimo
        if ($subtotal < $coupon['valor_minimo']) {
            return [
                'valid' => false, 
                'message' => "Valor mínimo para este cupom é R$ " . number_format($coupon['valor_minimo'], 2, ',', '.')
            ];
        }
        
        // Calcular desconto
        $discount = 0;
        if ($coupon['tipo_desconto'] === 'percentual') {
            $discount = ($subtotal * $coupon['desconto']) / 100;
        } else {
            $discount = $coupon['desconto'];
        }
        
        // Garantir que o desconto não seja maior que o subtotal
        $discount = min($discount, $subtotal);
        
        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Cupom aplicado com sucesso!'
        ];
    }
    
    public function getCouponByCode($code) {
        $stmt = $this->db->prepare("SELECT * FROM cupons WHERE codigo = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }

    public function getAllCoupons() {
        $stmt = $this->db->prepare("SELECT * FROM cupons ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getActiveCoupons() {
        $stmt = $this->db->prepare("SELECT * FROM cupons WHERE ativo = 1 AND validade >= CURDATE() ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getExpiredCoupons() {
        $stmt = $this->db->prepare("SELECT * FROM cupons WHERE validade < CURDATE() ORDER BY validade DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function toggleCouponStatus($id) {
        $coupon = $this->findById($id);
        if (!$coupon) {
            return false;
        }
        
        $newStatus = $coupon['ativo'] ? 0 : 1;
        return $this->update($id, ['ativo' => $newStatus]);
    }
    
    public function createCoupon($data) {
        // Verificar se o código já existe
        if ($this->getCouponByCode($data['codigo'])) {
            throw new Exception('Código do cupom já existe');
        }
        
        return $this->create($data);
    }
    
    public function updateCoupon($id, $data) {
        // Se está alterando o código, verificar se não existe outro cupom com o mesmo código
        if (isset($data['codigo'])) {
            $existingCoupon = $this->getCouponByCode($data['codigo']);
            if ($existingCoupon && $existingCoupon['id'] != $id) {
                throw new Exception('Código do cupom já existe');
            }
        }
        
        return $this->update($id, $data);
    }
    
    public function getCouponStats() {
        $sql = "SELECT 
                    COUNT(*) as total_cupons,
                    SUM(CASE WHEN ativo = 1 AND validade >= CURDATE() THEN 1 ELSE 0 END) as ativos,
                    SUM(CASE WHEN ativo = 0 THEN 1 ELSE 0 END) as inativos,
                    SUM(CASE WHEN validade < CURDATE() THEN 1 ELSE 0 END) as expirados
                FROM cupons";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch();
    }
}

