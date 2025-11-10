<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/cadastrar-contato.php");
    exit();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: ../paginas/index.php"); 
    exit();
}

// --- 2. Coleta de Dados ---
$usuarioID = $_SESSION['id_usuario'];
$nome = $_POST['nome'] ?? '';
$telefone = $_POST['telefone'] ?? '';

// Função helper para redirecionar com erro
function redirect_error($msg) {
    $erroMsg = urlencode($msg);
    header("Location: ../paginas/cadastrar-contato.php?status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Validação ---
if (empty($nome) || empty($telefone)) {
    redirect_error('Erro: Nome e Telefone são obrigatórios.');
}
if (empty($usuarioID)) {
    redirect_error('Erro: Sua sessão expirou. Faça login novamente.');
}

// --- 4. Inserção no Banco (Tabela 'ContatosEmergencia') ---
try {
    $sql = "INSERT INTO ContatosEmergencia (UsuarioID, Nome, telefone) 
            VALUES (:usuarioID, :nome, :telefone)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    
    $stmt->execute();

    // --- 5. Resposta de Sucesso (REDIRECIONAMENTO) ---
    header("Location: ../paginas/cadastrar-contato.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // --- 6. Resposta de Erro (REDIRECIONAMENTO) ---
    redirect_error('Erro de banco de dados: ' . $e->getMessage());
}
?>