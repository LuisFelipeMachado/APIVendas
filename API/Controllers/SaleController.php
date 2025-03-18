<?php
namespace Controllers;

use Common\config;
use Common\Auth;
use PDO;
use PDOException;

class SaleController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnection();
    }

    public function index() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM vendas");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    public function show($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vendas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $sale = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$sale) {
                return ['error' => 'Venda não encontrada'];
            }
            return $sale;
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    #Criar uma nova venda
    public function store($userId, $products) {
        try {
            $this->pdo->beginTransaction();
            
            $stmt = $this->pdo->prepare("INSERT INTO vendas (user_id, total_amount) VALUES (:user_id, 0) RETURNING id");
            $stmt->execute(['user_id' => $userId]);
            $saleId = $stmt->fetchColumn();
            
        $totalAmount = 0;
        foreach ($products as $product) {
            $stmt = $this->pdo->prepare("SELECT price FROM produtos WHERE id = :id");
            $stmt->execute(['id' => $product['id']]);
            $price = $stmt->fetchColumn();
            $totalAmount += $price * $product['quantity'];

            $stmt = $this->pdo->prepare("INSERT INTO vendas_produtos (sale_id, product_id, quantity, price) VALUES (:sale_id, :product_id, :quantity, :price)");
             $stmt->execute([
            'sale_id' => $saleId,
            'product_id' => $product['id'],
            'quantity' => $product['quantity'],
            'price' => $price
              ]);
            }
            
            $stmt = $this->pdo->prepare("UPDATE vendas SET total_amount = :total WHERE id = :id");
            $stmt->execute(['total' => $totalAmount, 'id' => $saleId]);
            
            $this->pdo->commit();
            return ['message' => 'Venda criada com sucesso', 'sale_id' => $saleId];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return ['error' => $e->getMessage()];
        }
    }
}
