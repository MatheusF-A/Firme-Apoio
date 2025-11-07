<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

header('Content-Type: application/json');

// Função auxiliar
function send_json_response($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response('error', 'Método de requisição inválido.');
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    send_json_response('error', 'Acesso não autorizado.');
}

// --- 2. Coleta de Dados ---
$titulo = $_POST['titulo'] ?? '';
$url_video = $_POST['url_video'] ?? ''; // O link completo do YouTube
// $autor foi removido

// Validação
if (empty($titulo) || empty($url_video)) {
    send_json_response('error', 'Erro: Título e URL são obrigatórios.');
}
if (strpos($url_video, 'youtube.com') === false && strpos($url_video, 'youtu.be') === false) {
     send_json_response('error', 'Erro: A URL fornecida não parece ser um vídeo do YouTube.');
}

// --- 3. Inserção no Banco (Tabela 'video' do TCC, sem autor) ---
try {
    // Nomes das colunas conforme Tabela 'Videos' (Quadro 14) [cite: 669-707]
    // SQL MODIFICADO: 'autor' foi removido
    $sql = "INSERT INTO videos (titulo, link) 
            VALUES (:titulo, :link)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':link', $url_video);
    
    $stmt->execute();

    // --- 4. Resposta de Sucesso ---
    send_json_response('success', 'Vídeo cadastrado com sucesso!');

} catch (Exception $e) {
    send_json_response('error', 'Erro ao salvar o vídeo: ' . $e->getMessage());
}
?>