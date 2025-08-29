<?php
//var_dump('teste');exit;

// Incluir configurações
require_once __DIR__ . '/config/config.php';

try {
    // Verificar se o controller existe
    if (!isset($controllers[$controller])) {
        throw new Exception("Controller não encontrado: {$controller}");
    }
    
    $controllerClass = $controllers[$controller];
    
    // Verificar se a classe do controller existe
    if (!class_exists($controllerClass)) {
        throw new Exception("Classe do controller não encontrada: {$controllerClass}");
    }
    
    // Instanciar controller
    $controllerInstance = new $controllerClass();
    
    // Verificar se o método existe
    if (!method_exists($controllerInstance, $action)) {
        throw new Exception("Ação não encontrada: {$action}");
    }
    
    // Executar ação
    $controllerInstance->$action();
    
} catch (Exception $e) {
    // Em caso de erro, mostrar página de erro ou redirecionar para home
    http_response_code(404);
    echo "<h1>Erro 404</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='".BASE_URL."'>Voltar ao início</a>";
}

