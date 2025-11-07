<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// Define o cabeçalho de resposta como JSON
header('Content-Type: application/json');

// Função auxiliar
function send_json_response($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

// --- 1. Segurança ---
// Alterado para POST, para seguir o padrão do fetch
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
     send_json_response('error', 'Método inválido.');
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    send_json_response('error', 'Acesso não autorizado.');
}

// O ID agora virá do 'body' do fetch (POST)
$videoID = $_POST['id'] ?? 0;

if (empty($videoID)) {
    send_json_response('error', 'ID do vídeo não fornecido.');
}

// --- 2. Deletar do Banco (usando a coluna 'id') ---
try {
    $sql = "DELETE FROM videos WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $videoID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        send_json_response('success', 'Vídeo deletado com sucesso!');
    } else {
        send_json_response('error', 'Vídeo não encontrado ou permissão negada.');
    }

} catch (Exception $e) {
    send_json_response('error', 'Erro de banco de dados: ' . $e->getMessage());
}
?>