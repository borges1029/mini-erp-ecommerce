<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Stock.php';
require_once __DIR__ . '/../models/Coupon.php';

class HomeController extends BaseController {
    
    public function index() {
        try {
            $productModel = new Product();
            $orderModel = new Order();
            $stockModel = new Stock();
            $couponModel = new Coupon();
            
            // Estatísticas do dashboard
            $stats = [
                'total_produtos' => $productModel->count(),
                'pedidos' => $orderModel->getOrderStats(),
                'cupons' => $couponModel->getCouponStats(),
                'estoque_baixo' => $stockModel->getLowStockItems(10)
            ];
            
            // Produtos recentes
            $recentProducts = $productModel->getProductsWithStock();
            $recentProducts = array_slice($recentProducts, 0, 5);
            
            // Pedidos recentes
            $recentOrders = $orderModel->findAll();
            $recentOrders = array_slice($recentOrders, 0, 5);
            
            $this->view('home/index', [
                'title' => 'Mini ERP - Dashboard',
                'stats' => $stats,
                'recentProducts' => $recentProducts,
                'recentOrders' => $recentOrders
            ]);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Erro ao carregar dashboard: ' . $e->getMessage());
            $this->view('home/index', [
                'title' => 'Mini ERP - Dashboard',
                'stats' => [],
                'recentProducts' => [],
                'recentOrders' => []
            ]);
        }
    }
    
    public function catalog() {
        try {
            $productModel = new Product();
            $products = $productModel->getProductsWithStock();
            
            $this->view('home/catalog', [
                'title' => 'Catálogo de Produtos',
                'products' => $products
            ]);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Erro ao carregar catálogo: ' . $e->getMessage());
            $this->view('home/catalog', [
                'title' => 'Catálogo de Produtos',
                'products' => []
            ]);
        }
    }
}

