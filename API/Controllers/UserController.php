<?php
namespace Controllers;

use Common\config;
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

            # Verifica se os campos estão presentes
            if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
                return ['error' => 'Os campos name, email e password são obrigatórios'];
            }

            #Hash da senha antes de armazenar
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $hashedPassword
            ]);
            
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