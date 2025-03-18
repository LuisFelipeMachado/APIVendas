<?php
namespace Controllers;

use Common\Config;
use Common\Auth;
use PDO;
use PDOException;

class UserController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnection();
    }
    #Cria um novo usuário
    public function register($data) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (name, email, password) VALUES (:name, :email, :password)");
            
            #Hash da senha antes de armazenar
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $stmt->execute($data);
            return ['message' => 'Usuário criado com sucesso'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    #Lista todos os usuários TESTE
     public function index() {
        try {
            $stmt = $this->pdo->query("SELECT id, name, email, created_at FROM usuarios");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    } 
    #Buscar um usuário por ID
    public function show($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, created_at FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
    #Deletar um usuário
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return ['message' => 'Usuário removido com sucesso'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}