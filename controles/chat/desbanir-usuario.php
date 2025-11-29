<?php

session_start();
require_once('../../config/conexao.php');
header('Content-Type: application/json');

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    echo json_encode(['status' => 'error', 'message' => 'Proibido']); exit;
}

$id = $_POST['id_alvo'] ?? 0;

try {
    $stmt = $conn->prepare("UPDATE usuario SET chat_banido = 0 WHERE usuarioID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro DB']);
}
?>