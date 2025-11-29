<?php
// controles/chat/enviar-mensagem.php
session_start();
require_once('../../config/conexao.php');

header('Content-Type: application/json; charset=utf-8');

// 1. Validações Básicas
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit; }

$mensagem = trim($_POST['mensagem'] ?? '');
if (empty($mensagem)) {
    echo json_encode(['status' => 'error', 'message' => 'Mensagem vazia']);
    exit;
}

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['perfil'])) {
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado']);
    exit;
}

// 2. Define onde salvar (Usuário ou Voluntário)
try {
    if ($_SESSION['perfil'] === 'voluntario') {
        // Voluntário envia
        $stmt = $conn->prepare("INSERT INTO chat_mensagens (voluntarioID, mensagem) VALUES (:id, :msg)");
        $stmt->bindParam(':id', $_SESSION['id_usuario']);

    } else {
        // Usuário envia
        $stmt = $conn->prepare("INSERT INTO chat_mensagens (usuarioID, mensagem) VALUES (:id, :msg)");
        $stmt->bindParam(':id', $_SESSION['id_usuario']);
    }

    $stmt->bindParam(':msg', $mensagem);
    $stmt->execute();

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro DB']);
}
?>