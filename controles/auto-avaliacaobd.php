<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

header('Content-Type: application/json');

// Função auxiliar para respostas JSON
function send_json_response($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response('error', 'Método de requisição inválido.');
}
// Apenas usuários logados (e que são 'usuario') podem enviar
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    send_json_response('error', 'Acesso não autorizado.');
}
if (!isset($_SESSION['id_usuario'])) {
    send_json_response('error', 'Sessão inválida.');
}

// --- 2. Coleta de Dados ---
$usuarioID = $_SESSION['id_usuario'];
$notaHumor = $_POST['notaHumor'] ?? null;
$perguntaUm = $_POST['perguntaUm'] ?? '';
$perguntaDois = $_POST['perguntaDois'] ?? '';
$perguntaTres = $_POST['perguntaTres'] ?? '';
$dataRealizacao = date('Y-m-d H:i:s'); // Data atual

// --- 3. Validação de Dados ---
if (empty($usuarioID) || empty($notaHumor)) {
    send_json_response('error', 'Erro: Nota de humor ou ID do usuário estão ausentes.');
}
if (empty($perguntaUm) || empty($perguntaDois) || empty($perguntaTres)) {
    send_json_response('error', 'Erro: Todas as perguntas devem ser respondidas.');
}

try {
    // Nomes das colunas conforme o Dicionário de Dados (Quadro 21) [cite: 810-822]
    $sql = "INSERT INTO autoavaliacao (UsuarioID, dataRealizacao, notaHumor, PerguntaUm, PerguntaDois, PerguntaTres) 
            VALUES (:usuarioID, :dataRealizacao, :notaHumor, :perguntaUm, :perguntaDois, :perguntaTres)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->bindParam(':dataRealizacao', $dataRealizacao);
    $stmt->bindParam(':notaHumor', $notaHumor, PDO::PARAM_INT);
    $stmt->bindParam(':perguntaUm', $perguntaUm);
    $stmt->bindParam(':perguntaDois', $perguntaDois);
    $stmt->bindParam(':perguntaTres', $perguntaTres);
    
    $stmt->execute();

    // --- 5. Resposta de Sucesso ---
    send_json_response('success', 'Sua autoavaliação foi enviada com sucesso!');

} catch (Exception $e) {
    send_json_response('error', 'Erro ao salvar a avaliação: ' . $e->getMessage());
}
?>