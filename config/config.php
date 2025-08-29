<?php

// Configurações gerais da aplicação
define("BASE_URL", "http://localhost/mini-erp");
define('SITE_NAME', 'Mini ERP');
define('EMAIL_SERVER', 'sandbox.smtp.mailtrap.io');
define('EMAIL_PORT', 587);
define('EMAIL_USERNAME', 'a502f9f5162241');
define('EMAIL_PASSWORD', '8cdf2c0e434541');
define('FROM_EMAIL', 'testedev@email.com');

// Iniciar sessão
session_start();

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurar exibição de erros (remover em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

// Autoload simples para classes
spl_autoload_register(function ($className) {
    $paths = [
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../config/'
    ];

    foreach ($paths as $path) {
        $file = $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

//

// Roteamento simples
$controller = $_GET['p'] ?? 'home'; //Controller
$action = $_GET['a'] ?? 'index'; //Action

// Mapear controllers
$controllers = [
    'home' => 'HomeController',
    'product' => 'ProductController',
    'order' => 'OrderController',
    'coupon' => 'CouponController',
    'cart' => 'CartController',
    'checkout' => 'CheckoutController',
    'email' => 'CheckoutController',
    'webhook' => 'WebhookController'
];


