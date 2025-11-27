<?php
// controles/listar-mensagens.php
session_start();
require_once('../../config/conexao.php');

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([]);
    exit;
}

$meuID = $_SESSION['id_usuario'];

try {
    // Busca as últimas 50 mensagens
    // JOIN com usuario para pegar o chat_nickname
    $sql = "SELECT m.mensagemID, m.mensagem, m.dataEnvio, m.usuarioID, u.chat_nickname 
            FROM chat_mensagens m
            JOIN usuario u ON m.usuarioID = u.usuarioID
            ORDER BY m.mensagemID ASC"; // Ordem cronológica
            
    $stmt = $conn->query($sql);
    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultado = [];

    foreach ($mensagens as $msg) {
        $resultado[] = [
            'id' => $msg['mensagemID'],
            'texto' => htmlspecialchars($msg['mensagem']), // Sanitiza XSS aqui
            'autor' => htmlspecialchars($msg['chat_nickname']),
            'hora' => date('H:i', strtotime($msg['dataEnvio'])),
            'sou_eu' => ($msg['usuarioID'] == $meuID) // Flag para o CSS saber pintar de outra cor
        ];
    }

    echo json_encode($resultado);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>