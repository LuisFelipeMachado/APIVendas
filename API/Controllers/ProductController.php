<?php

namespace Controllers;

use Common\Config;
use Common\Auth;
use PDO;
use PDOException;

class ProductController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnection();
    }

    #Listar todos os produtos
    public function index() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM produtos");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    
    public function show($id) {
         try {
            $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
   
    public function store($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO produtos (name, description, price, stock) VALUES (:name, :description, :price, :stock)");
            $stmt->execute($data);
            return ['message' => 'Produto criado com sucesso'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
       }
    }

    public function update($id, $data) {
        try {
            $stmt = $this->pdo->prepare("UPDATE produtos SET name = :name, description = :description, price = :price, stock = :stock WHERE id = :id");
            $data['id'] = $id;
            $stmt->execute($data);
            return ['message' => 'Produto atualizado com sucesso'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
  
    public function delete($table, $id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return ['message' => ucfirst(rtrim($table, 's')) . ' removido com sucesso'];
            } else {
                return ['error' => ucfirst(rtrim($table, 's')) . ' não encontrado'];
            }
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];}
        }
}
