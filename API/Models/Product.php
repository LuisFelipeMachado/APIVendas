<?php
namespace Models;

use Common\Config;
use PDO;
use PDOException;

class Product {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnection();
    }

    public function createProduct($name, $description, $price, $stock) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO produtos (name, description, price, stock) VALUES (:name, :description, :price, :stock)");
            $stmt->execute(['name' => $name, 'description' => $description, 'price' => $price, 'stock' => $stock]);
            return ['message' => 'Produto criado com sucesso'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllProducts() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM produtos");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}