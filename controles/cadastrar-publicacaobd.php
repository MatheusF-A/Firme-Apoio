<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/cadastrar-publicacao.php");
    exit();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}

// --- 2. Coleta de Dados ---
$titulo = $_POST['titulo'] ?? '';
$subtitulo = $_POST['subtitulo'] ?? '';
$autor = $_POST['autor'] ?? '';
$link = $_POST['link'] ?? null; // Opcional
$texto = $_POST['texto'] ?? '';

// Função helper para redirecionar com erro
function redirect_error($msg) {
    $erroMsg = urlencode($msg);
    header("Location: ../paginas/cadastrar-publicacao.php?status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Validação (Campos obrigatórios)
if (empty($titulo) || empty($subtitulo) || empty($autor) || empty($texto)) {
    redirect_error('Erro: Título, Subtítulo, Autor e Texto são obrigatórios.');
}

// --- 4. Processamento da Imagem (Opcional) ---
$imagem_blob = null;
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { 
        redirect_error('Erro: A imagem é muito grande (Máx 5MB).');
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['imagem']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        redirect_error('Erro: Tipo de arquivo inválido (Apenas JPEG, PNG, GIF).');
    }
    $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
} 

// --- 5. Inserção no Banco (Tabela 'publicacao') ---
try {
    $sql = "INSERT INTO publicacao (titulo, subtitulo, autor, link, texto, imagem) 
            VALUES (:titulo, :subtitulo, :autor, :link, :texto, :imagem)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':subtitulo', $subtitulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':link', $link); 
    $stmt->bindParam(':texto', $texto);
    $stmt->bindParam(':imagem', $imagem_blob, PDO::PARAM_LOB); 
    
    $stmt->execute();

    // --- 6. Resposta de Sucesso (REDIRECIONAMENTO) ---
    header("Location: ../paginas/cadastrar-publicacao.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // --- 7. Resposta de Erro (REDIRECIONAMENTO) ---
    redirect_error('Erro de banco de dados: ' . $e->getMessage());
}
?>