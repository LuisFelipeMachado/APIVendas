<?php
namespace Common;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use PDOException;

require __DIR__ . '/../../vendor/autoload.php';
require_once 'database.php';

class Auth {
    private $pdo;
    private $secretKey = 'sua_chave_secreta_aqui'; 

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, password FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($password, $user['password'])) {
                return ['error' => 'Credenciais inválidas'];
        }
            $token = $this->generateJWT($user['id']);
            return ['token' => $token];
        } catch (PDOException $e) {
            return ['error' => 'Erro no login: ' . $e->getMessage()];
        }
    }

    private function generateJWT($userId) {
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
}