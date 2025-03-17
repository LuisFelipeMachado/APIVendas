<?php

// Dados de conexão
$host = 'localhost'; // O host do banco de dados
$dbname = 'usuarios'; // Nome do banco de dados
$username = 'postgres'; // Usuário
$password = 'super_senha'; // Senha (ajuste para a senha que você configurou no PostgreSQL)

// Conexão com o banco de dados
try {
    // Criar conexão com PDO (PostgreSQL)
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    
    // Definir o modo de erro do PDO para exceção
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Se a conexão for bem-sucedida, você pode rodar uma consulta simples para testar
    $result = $pdo->query('SELECT 1'); // Uma consulta simples para verificar a conexão

    // Verifique se a consulta foi bem-sucedida
    if ($result) {
        echo "Conexão bem-sucedida com o banco de dados '$dbname'.";
    } else {
        echo "Falha ao executar a consulta.";
    }
} catch (PDOException $e) {
    // Captura o erro de conexão
    echo "Erro na conexão: " . $e->getMessage();
}

