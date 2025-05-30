<?php

namespace Common;

use PDO;
use PDOException;

$host = 'localhost';
$dbname = 'usuarios'; 
$username = 'postgres'; 
$password = 'xxz2005.A'; 


try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $result = $pdo->query('SELECT 1'); 

    if ($result) {
        echo "Conexão bem-sucedida com o banco de dados '$dbname'.";
    } else {
        echo "Falha ao executar a consulta.";
    }
} catch (PDOException $e) {

    echo "Erro na conexão: " . $e->getMessage();
}

