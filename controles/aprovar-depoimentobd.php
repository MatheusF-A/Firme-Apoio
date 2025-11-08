<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../paginas/gerenciar-depoimentos.php?status=erro&msg=ID nao fornecido");
    exit();
}

$depoimentoID = $_GET['id'];

// --- 2. Atualizar no Banco (UPDATE) ---
try {
    $sql = "UPDATE depoimentos SET aprovado = 1 WHERE depoimentoID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $depoimentoID, PDO::PARAM_INT);
    $stmt->execute();

    // --- 3. Redirecionar com Sucesso ---
    header("Location: ../paginas/gerenciar-depoimentos.php?status=aprovado");
    exit();

} catch (Exception $e) {
    // --- 4. Redirecionar com Erro ---
    $erroMsg = urlencode("Erro ao aprovar: " . $e->getMessage());
    header("Location: ../paginas/gerenciar-depoimentos.php?status=erro&msg={$erroMsg}");
    exit();
}
?>