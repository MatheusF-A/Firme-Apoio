<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/cadastrar-depoimento.php");
    exit();
}
// Apenas usuários podem postar
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: ../paginas/index.php"); 
    exit();
}

// --- 2. Coleta de Dados ---
$usuarioID = $_SESSION['id_usuario']; // Vincula ao usuário logado
$titulo = $_POST['titulo'] ?? '';
$texto = $_POST['texto'] ?? '';

// Função helper para redirecionar com erro
function redirect_error($msg) {
    $erroMsg = urlencode($msg);
    header("Location: ../paginas/cadastrar-depoimento.php?status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Validação ---
if (empty($titulo) || empty($texto)) {
    redirect_error('Erro: Título e Texto são obrigatórios.');
}
if (empty($usuarioID)) {
    redirect_error('Erro: Sua sessão expirou. Faça login novamente.');
}

// --- 4. Inserção no Banco (Tabela 'depoimentos') ---
try {
    // 'aprovado' terá o valor DEFAULT 0 (conforme o SQL)
    $sql = "INSERT INTO depoimentos (usuarioID, titulo, texto) 
            VALUES (:usuarioID, :titulo, :texto)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':texto', $texto);
    
    $stmt->execute();

    // --- 5. Resposta de Sucesso (REDIRECIONAMENTO) ---
    header("Location: ../paginas/cadastrar-depoimento.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // --- 6. Resposta de Erro (REDIRECIONAMENTO) ---
    redirect_error('Erro de banco de dados: ' . $e->getMessage());
}
?>