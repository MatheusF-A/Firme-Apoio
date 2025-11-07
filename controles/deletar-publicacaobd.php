<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../paginas/conteudo-publicacao.php?status=erro&msg=ID nao fornecido");
    exit();
}

$id = $_GET['id'];

// --- 2. Deletar do Banco (Tabela 'publicacao') ---
try {
    $sql = "DELETE FROM publicacao WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // --- 3. Redirecionar com Sucesso ---
    header("Location: ../paginas/conteudo-publicacao.php?status=deletado");
    exit();

} catch (Exception $e) {
    // --- 4. Redirecionar com Erro ---
    $erroMsg = urlencode("Erro ao deletar: " . $e->getMessage());
    header("Location: ../paginas/conteudo-publicacao.php?status=erro&msg={$erroMsg}");
    exit();
}
?>