<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/conteudo-publicacao.php");
    exit();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}

// --- 2. Coleta de Dados ---
$id = $_POST['id'] ?? 0;
$titulo = $_POST['titulo'] ?? '';
$subtitulo = $_POST['subtitulo'] ?? '';
$autor = $_POST['autor'] ?? '';
$link = $_POST['link'] ?? null;
$texto = $_POST['texto'] ?? '';

// Função helper para redirecionar com erro
function redirect_error($id, $msg) {
    $erroMsg = urlencode($msg);
    header("Location: ../paginas/editar-publicacao.php?id={$id}&status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Validação ---
if (empty($id)) {
    redirect_error(0, "ID da publicacao ausente.");
}
if (empty($titulo) || empty($subtitulo) || empty($autor) || empty($texto)) {
    redirect_error($id, "Título, Subtítulo, Autor e Texto são obrigatórios.");
}

// --- 4. Processamento da Imagem (Apenas se uma nova for enviada) ---
$imagem_blob = null;
$sql_imagem_part = ""; // Parte da query SQL

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    
    // Validações da nova imagem
    if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { 
        redirect_error($id, "A nova imagem é muito grande (Máx 5MB).");
    }
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['imagem']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        redirect_error($id, "Tipo de arquivo inválido (Apenas JPEG, PNG, GIF).");
    }

    // Se a nova imagem for válida, prepara para o SQL
    $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
    $sql_imagem_part = ", Imagem = :imagem"; // Adiciona a atualização da imagem à query
}

// --- 5. Atualização no Banco de Dados (UPDATE) ---
try {
    // Query SQL dinâmica (atualiza a imagem apenas se $sql_imagem_part não estiver vazio)
    $sql = "UPDATE publicacao 
            SET titulo = :titulo, 
                subtitulo = :subtitulo, 
                autor = :autor, 
                link = :link, 
                texto = :texto
                {$sql_imagem_part}
            WHERE id = :id";
            
    $stmt = $conn->prepare($sql);
    
    // Bind dos parâmetros de texto e ID
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':subtitulo', $subtitulo);
    $stmt->bindParam(':autor', $autor);
    $stmt->bindParam(':link', $link);
    $stmt->bindParam(':texto', $texto);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // Bind da Imagem (APENAS se uma nova foi enviada)
    if ($imagem_blob !== null) {
        $stmt->bindParam(':imagem', $imagem_blob, PDO::PARAM_LOB);
    }
    
    $stmt->execute();

    // --- 6. Redirecionar com Sucesso ---
    header("Location: ../paginas/conteudo-publicacao.php?status=editado");
    exit();

} catch (Exception $e) {
    // --- 7. Redirecionar com Erro ---
    redirect_error($id, "Erro de banco de dados: " . $e->getMessage());
}
?>