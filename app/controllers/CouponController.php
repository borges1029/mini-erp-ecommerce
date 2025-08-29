<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Coupon.php';

class CouponController extends BaseController {
    private $couponModel;
    
    public function __construct() {
        $this->couponModel = new Coupon();
    }
    
    public function index() {
        $allCoupons = $this->couponModel->getAllCoupons();
        $activeCoupons = $this->couponModel->getActiveCoupons();
        $expiredCoupons = $this->couponModel->getExpiredCoupons();
        $stats = $this->couponModel->getCouponStats();
        
        $this->view('coupons/index', [
            'title' => 'Gerenciar Cupons',
            'allCoupons' => $allCoupons,
            'activeCoupons' => $activeCoupons,
            'expiredCoupons' => $expiredCoupons,
            'stats' => $stats
        ]);
    }
    
    public function create() {
        if ($this->isPost()) {
            $data = $this->sanitizeInput($this->getPostData());
            
            // Validar dados obrigatórios
            $errors = $this->validateRequired($data, ['codigo', 'desconto', 'tipo_desconto', 'validade']);
            
            if (empty($errors)) {
                try {
                    // Preparar dados do cupom
                    $couponData = [
                        'codigo' => strtoupper($data['codigo']),
                        'desconto' => floatval($data['desconto']),
                        'tipo_desconto' => $data['tipo_desconto'],
                        'valor_minimo' => floatval($data['valor_minimo'] ?? 0),
                        'validade' => $data['validade'],
                        'ativo' => isset($data['ativo']) ? 1 : 0
                    ];
                    
                    $couponId = $this->couponModel->createCoupon($couponData);
                    
                    $this->setFlashMessage('success', 'Cupom criado com sucesso!');
                    $this->redirect(BASE_URL . '/?p=coupon&a=index');
                    
                } catch (Exception $e) {
                    $this->setFlashMessage('error', 'Erro ao criar cupom: ' . $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $this->view('coupons/create', [
            'title' => 'Criar Cupom'
        ]);
    }
    
    public function edit() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->setFlashMessage('error', 'ID do cupom não informado');
            $this->redirect(BASE_URL . '/?p=coupon&a=index');
        }
        
        $coupon = $this->couponModel->findById($id);
        
        if (!$coupon) {
            $this->setFlashMessage('error', 'Cupom não encontrado');
            $this->redirect(BASE_URL . '/?p=coupon&a=index');
        }
        
        if ($this->isPost()) {
            $data = $this->sanitizeInput($this->getPostData());
            
            // Validar dados obrigatórios
            $errors = $this->validateRequired($data, ['codigo', 'desconto', 'tipo_desconto', 'validade']);
            
            if (empty($errors)) {
                try {
                    // Preparar dados do cupom
                    $couponData = [
                        'codigo' => strtoupper($data['codigo']),
                        'desconto' => floatval($data['desconto']),
                        'tipo_desconto' => $data['tipo_desconto'],
                        'valor_minimo' => floatval($data['valor_minimo'] ?? 0),
                        'validade' => $data['validade'],
                        'ativo' => isset($data['ativo']) ? 1 : 0
                    ];
                    
                    $this->couponModel->updateCoupon($id, $couponData);
                    
                    $this->setFlashMessage('success', 'Cupom atualizado com sucesso!');
                    $this->redirect(BASE_URL . '/?p=coupon&a=index');
                    
                } catch (Exception $e) {
                    $this->setFlashMessage('error', 'Erro ao atualizar cupom: ' . $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $this->view('coupons/create', [
            'coupon' => $coupon,
            'title' => 'Editar Cupom'
        ]);
    }
    
    public function delete() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID não informado']);
        }
        
        try {
            $this->couponModel->delete($id);
            $this->json(['success' => true, 'message' => 'Cupom excluído com sucesso']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erro ao excluir cupom: ' . $e->getMessage()]);
        }
    }
    
    public function toggle() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID não informado']);
        }
        
        try {
            $this->couponModel->toggleCouponStatus($id);
            $this->json(['success' => true, 'message' => 'Status do cupom alterado']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erro ao alterar status: ' . $e->getMessage()]);
        }
    }
    
    public function validate() {
        if (!$this->isPost()) {
            $this->json(['success' => false, 'message' => 'Método não permitido']);
        }
        
        $data = $this->getPostData();
        $code = trim($data['code'] ?? '');
        $subtotal = floatval($data['subtotal'] ?? 0);
        
        if (!$code) {
            $this->json(['success' => false, 'message' => 'Código do cupom é obrigatório']);
        }
        
        $validation = $this->couponModel->validateCoupon($code, $subtotal);
        
        $this->json($validation);
    }

    public function viewCoupon() {
        $id = $this->getParam('id');

        if (!$id) {
            $this->setFlashMessage('error', 'ID do cupom não informado');
            $this->redirect(BASE_URL . '/?p=coupon&a=index');
        }

        $coupon = $this->couponModel->findById($id);

        if (!$coupon) {
            $this->setFlashMessage('error', 'Cupom não encontrado');
            $this->redirect(BASE_URL . '/?p=coupon&a=index');
        }

        $this->view('coupons/create', [
            'coupon' => $coupon,
            'title' => 'Detalhes do Cupom'
        ]);
    }
}

