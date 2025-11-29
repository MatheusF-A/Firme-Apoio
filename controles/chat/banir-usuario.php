<?php
// controles/chat/banir-usuario.php
session_start();
require_once('../../config/conexao.php');
header('Content-Type: application/json');

// Segurança: Apenas Voluntário
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    echo json_encode(['status' => 'error', 'message' => 'Proibido']); exit;
}

$id = $_POST['id_alvo'] ?? 0;

try {
    // Marca como banido
    $stmt = $conn->prepare("UPDATE usuario SET chat_banido = 1 WHERE usuarioID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro ao banir']);
}
?>