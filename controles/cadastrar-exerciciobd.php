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
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario' || !isset($_SESSION['id_usuario'])) {
    send_json_response('error', 'Acesso não autorizado.');
}

// --- 2. Coleta de Dados ---
$usuarioID = $_SESSION['id_usuario'];
$nome = $_POST['nome'] ?? '';
$detalhes = $_POST['detalhes'] ?? '';
$frequenciaID = $_POST['frequenciaID'] ?? null; // Pega o ID do dropdown

// Validação
if (empty($nome) || empty($frequenciaID)) {
    send_json_response('error', 'Erro: Nome e Frequência são obrigatórios.');
}

// --- 3. Processamento da Imagem (Opcional) ---
$imagem_blob = null;
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { 
        send_json_response('error', 'Erro: A imagem é muito grande (Máx 5MB).');
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['imagem']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        send_json_response('error', 'Erro: Tipo de arquivo inválido (Apenas JPEG, PNG, GIF).');
    }
    $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
} 

// --- 4. Inserção no Banco de Dados (Tabela exercicios) ---
try {
    // Usando as colunas da tabela exercicios
    $sql = "INSERT INTO exercicios (usuarioID, nome, detalhes, frequenciaID, imagem, concluido) 
            VALUES (:usuarioID, :nome, :detalhes, :frequenciaID, :imagem, 0)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':detalhes', $detalhes);
    $stmt->bindParam(':frequenciaID', $frequenciaID, PDO::PARAM_INT);
    $stmt->bindParam(':imagem', $imagem_blob, PDO::PARAM_LOB);
    
    $stmt->execute();

    // --- 5. Resposta de Sucesso ---
    send_json_response('success', 'Novo exercício cadastrado com sucesso!');

} catch (Exception $e) {
    // Verifica se foi erro de FK (ex: frequenciaID não existe)
    if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
         send_json_response('error', 'Erro: A frequência selecionada é inválida.');
    }
    send_json_response('error', 'Erro ao salvar o exercício: ' . $e->getMessage());
}
?>