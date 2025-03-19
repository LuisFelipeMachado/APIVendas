<?php
namespace Common;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use PDOException;
use Exception;

require __DIR__ . '/../../vendor/autoload.php';
require_once 'database.php';

class Auth {
    private $pdo;
    private $secretKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3NDIzMzE2MzMsImV4cCI6MTc0MjMzNTIzMywic3ViIjo3fQ.6WtKcg7iZbll6Wkph8ABw0-DpdrVHvyjWLQdmQJGu8k'; 

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, password FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || crypt($password, $user['password']) !== $user['password']) {
                return ['error' => 'Credenciais inválidas'];
            }
            $token = $this->generateJWT($user['id']);
            return ['token' => $token];
        } catch (PDOException $e) {
            return ['error' => 'Erro no login: ' . $e->getMessage()];
        }
    }

    private function generateJWT($userId): string {
        $payload = [
            'iat' => time(), 
            'exp' => time() + (60 * 60), 
            'sub' => $userId
        ];
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $decoded->sub;
        } catch (Exception $e) {
            return false;
        }
    }
    public function protectRoute() {
    
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Acesso negado! Token não enviado.']);
            exit;
        }
    
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
    
    
        $userId = $this->validateToken($token);
        if (!$userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Token inválido ou expirado.']);
            exit;
        }
    
      
        return $userId;
    }    
    
}
