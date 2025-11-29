<?php

session_start();
require_once('../../config/conexao.php');
header('Content-Type: application/json; charset=utf-8');

// Segurança
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    echo json_encode(['status' => 'error', 'message' => 'Proibido']); exit;
}

try {
    $sql = "SELECT usuarioID, Nome, email, chat_nickname, dtCadastro 
            FROM usuario 
            WHERE chat_banido = 1 
            ORDER BY Nome ASC";
            
    $stmt = $conn->query($sql);
    $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Formata a data
    foreach($lista as &$user) {
        $user['dataCadastro'] = date('d/m/Y', strtotime($user['dataCadastro']));
    }

    echo json_encode(['status' => 'success', 'dados' => $lista]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro DB']);
}
?>