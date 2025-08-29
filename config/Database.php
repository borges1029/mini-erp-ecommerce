<?php

class Database {
    private static $instance = null;
    private $connection;

    private $host = 'localhost:3307'; // Retirar a porta caso o banco utilize a porta padrão
    private $database = 'mini_erp';
    private $username = 'root';
    private $password = '';
    
    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            // Se chegou aqui, conexão deu certo
            return;
        } catch (PDOException $e) {
            // Continua tentando com a próxima porta
        }

        // Se nenhuma porta funcionou
        die("Erro na conexão com o banco de dados: Não foi possível conectar nas portas 3306 ou 3307.");
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    // Previne clonagem da instância
    private function __clone() {}
    
    // Previne deserialização da instância
    public function __wakeup() {}
}
