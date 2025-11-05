<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/ajuda-externa.php");
    exit();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}

// --- 2. Coleta de Dados (Incluindo o ID) ---
$localID = $_POST['localID'] ?? 0;
$nome = $_POST['nome'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$horario = $_POST['horario'] ?? '';

// Função helper para redirecionar com erro
function redirect_error($id, $msg) {
    $erroMsg = urlencode($msg);
    header("Location: ../paginas/editar-local.php?id={$id}&status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Validação ---
if (empty($localID)) {
    redirect_error(0, "ID do local ausente.");
}
if (empty($nome) || empty($descricao) || empty($endereco) || empty($telefone) || empty($horario)) {
    redirect_error($localID, "Todos os campos (exceto email) são obrigatórios.");
}

// --- 4. Processamento da Imagem (Apenas se uma nova for enviada) ---
$imagem_blob = null;
$sql_imagem_part = ""; // Parte da query SQL (se a imagem for atualizada)

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    
    // Validações da nova imagem
    if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { 
        redirect_error($localID, "A nova imagem é muito grande (Máx 5MB).");
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['imagem']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        redirect_error($localID, "Tipo de arquivo inválido (Apenas JPEG, PNG, GIF).");
    }

    // Se a nova imagem for válida, prepara para o SQL
    $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
    $sql_imagem_part = ", imagem = :imagem"; // Adiciona a atualização da imagem à query
}

// --- 5. Atualização no Banco de Dados (UPDATE) ---
try {
    // Query SQL dinâmica (atualiza a imagem apenas se $sql_imagem_part não estiver vazio)
    $sql = "UPDATE locaisajuda 
            SET nome = :nome, 
                descricao = :descricao, 
                endereco = :endereco, 
                telefone = :telefone, 
                email = :email, 
                horario = :horario
                {$sql_imagem_part}
            WHERE localID = :localID";
            
    $stmt = $conn->prepare($sql);
    
    // Bind dos parâmetros de texto e ID
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':horario', $horario);
    $stmt->bindParam(':localID', $localID, PDO::PARAM_INT);
    
    // Bind da Imagem (APENAS se uma nova foi enviada)
    if ($imagem_blob !== null) {
        $stmt->bindParam(':imagem', $imagem_blob, PDO::PARAM_LOB);
    }
    
    $stmt->execute();

    // --- 6. Redirecionar com Sucesso ---
    header("Location: ../paginas/ajuda-externa.php?status=editado");
    exit();

} catch (Exception $e) {
    // --- 7. Redirecionar com Erro ---
    redirect_error($localID, "Erro de banco de dados: " . $e->getMessage());
}
?>