<?php

class BaseController {
    
    public function view($viewName, $data = []) {
        // Extrair dados para variáveis
        extract($data);
        
        // Incluir o arquivo de view
        $viewFile = __DIR__ . "/../views/{$viewName}.php";
        
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die("Página não encontrada <a href='".BASE_URL."'>clique aqui para voltar</a>");
            //die("View não encontrada: {$viewName}");
        }
    }

    public function redirect($url) {
        header("Location: {$url}");
        exit;
    }

    public function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[] = "Campo '{$field}' é obrigatório";
            }
        }
        
        return $errors;
    }

    public function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public function getParam($name, $default = null) {
        return $_GET[$name] ?? $default;
    }

    public function getPostData() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }

        return $_POST;
    }

    public function setFlashMessage($type, $message) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash'][$type] = $message;
    }

    public function getFlashMessages() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}

