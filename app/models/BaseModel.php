<?php

require_once __DIR__ . '/../../config/Database.php';

abstract class BaseModel {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $fields = array_keys($data);
        $placeholders = str_repeat('?,', count($fields) - 1) . '?';
        
        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute(array_values($data))) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function update($id, $data) {
        $fields = array_keys($data);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $sql = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        $values = array_values($data);
        $values[] = $id;
        
        return $stmt->execute($values);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function count() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}

