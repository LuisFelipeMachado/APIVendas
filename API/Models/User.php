<?php

namespace Models;

use Common\Config;
use PDO;
use PDOException;

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnection();
    }

    public function createUser($name, $email, $password) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (name, email, password) VALUES (:name, :email, :password)");
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashedPassword]);
            return ['message' => 'Usuário criado com sucesso'];
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getUserByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}