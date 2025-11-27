<?php

session_start();
require_once('../../config/conexao.php');

header('Content-Type: application/json');

// 1. Verificações de Segurança
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido']);
    exit;
}

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'usuario') {
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado']);
    exit;
}

// 2. Coleta e Sanitização
$usuarioID = $_SESSION['id_usuario'];
$mensagem = trim($_POST['mensagem'] ?? '');

if (empty($mensagem)) {
    echo json_encode(['status' => 'error', 'message' => 'Mensagem vazia']);
    exit;
}

try {
    // 3. Inserção no Banco
    $sql = "INSERT INTO chat_mensagens (usuarioID, mensagem) VALUES (:id, :msg)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $usuarioID);
    $stmt->bindParam(':msg', $mensagem);
    $stmt->execute();

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar']);
}
?>