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
$contatoID = $_POST['contatoID'] ?? 0;
$nome = $_POST['nome'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$usuarioID = $_SESSION['id_usuario'];

// Função helper para redirecionar com erro
function redirect_error($id, $msg) {
    $erroMsg = urlencode($msg);
    header("Location: ../paginas/editar-contato.php?id={$id}&status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Validação ---
if (empty($contatoID)) {
    redirect_error(0, "ID do contato ausente.");
}
if (empty($nome) || empty($telefone)) {
    redirect_error($contatoID, "Nome e Telefone são obrigatórios.");
}

// --- 4. Atualização no Banco (UPDATE) ---
try {
    // A query SÓ ATUALIZA se o ContatoID e o UsuarioID baterem
    $sql = "UPDATE ContatosEmergencia 
            SET Nome = :nome, 
                telefone = :telefone
            WHERE ContatoID = :contatoID AND UsuarioID = :usuarioID";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':contatoID', $contatoID, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
         throw new Exception("Nao foi possivel atualizar (contato nao encontrado ou permissao negada).");
    }

    // --- 5. Redirecionar com Sucesso ---
    header("Location: ../paginas/cadastrar-contato.php?status=editado");
    exit();

} catch (Exception $e) {
    // --- 6. Redirecionar com Erro ---
    redirect_error($contatoID, "Erro de banco de dados: " . $e->getMessage());
}
?>