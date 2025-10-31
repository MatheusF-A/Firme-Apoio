<?php

$host = 'localhost';
$dbname = 'firme_apoio';
$username = 'root';
$password = '';

try {
    // Criação da conexão com o banco de dados usando PDO
    // Ajustado para utf8mb4, conforme seu script de criação de tabelas
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Configuração do modo de erro do PDO para exceções
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Garantir que a conexão use utf8mb4
    $conn->exec("SET NAMES 'utf8mb4'");

    // echo 'Conexão bem-sucedida!';
    
} catch (PDOException $e) {
    // Capturar e exibir qualquer erro de conexão
    echo 'Erro na conexão: ' . $e->getMessage();
    // Encerrar a execução do script em caso de erro
    die();
}

// A variável $conn está pronta para ser usada em outros arquivos.
?>