<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

header('Content-Type: application/json');

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
$habitoID = $_POST['habitoID'] ?? 0; // ID do hábito que está sendo editado
$nome = $_POST['nome'] ?? '';
$detalhes = $_POST['detalhes'] ?? '';
$frequenciaID = $_POST['frequenciaID'] ?? null;

// Validação
if (empty($nome) || empty($frequenciaID) || empty($habitoID)) {
    send_json_response('error', 'Erro: Nome, Frequência e ID do Hábito são obrigatórios.');
}

// --- 3. Processamento da Imagem (Opcional) ---
$imagem_blob = null;
$sql_imagem_part = ""; // Parte da query SQL

// Verifica se uma NOVA imagem foi enviada
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    // Validações...
    if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { 
        send_json_response('error', 'Erro: A imagem é muito grande (Máx 5MB).');
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['imagem']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        send_json_response('error', 'Erro: Tipo de arquivo inválido.');
    }
    
    // Se a nova imagem for válida, prepara para o SQL
    $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
    $sql_imagem_part = ", imagem = :imagem"; // Adiciona a atualização da imagem à query
} 

// --- 4. Atualização no Banco (UPDATE) ---
try {
    // Query SQL dinâmica (só atualiza a imagem se uma nova foi enviada)
    $sql = "UPDATE habitos 
            SET nome = :nome, 
                detalhes = :detalhes, 
                frequenciaID = :frequenciaID
                {$sql_imagem_part}
            WHERE habitoID = :habitoID AND usuarioID = :usuarioID";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':detalhes', $detalhes);
    $stmt->bindParam(':frequenciaID', $frequenciaID, PDO::PARAM_INT);
    $stmt->bindParam(':habitoID', $habitoID, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    
    // Bind da Imagem (APENAS se uma nova foi enviada)
    if ($imagem_blob !== null) {
        $stmt->bindParam(':imagem', $imagem_blob, PDO::PARAM_LOB);
    }
    
    $stmt->execute();

    // --- 5. Resposta de Sucesso ---
    send_json_response('success', 'Hábito atualizado com sucesso!');

} catch (Exception $e) {
    send_json_response('error', 'Erro ao atualizar o hábito: ' . $e->getMessage());
}
?>