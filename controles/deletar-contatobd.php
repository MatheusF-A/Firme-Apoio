<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: ../paginas/index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../paginas/cadastrar-contato.php?status=erro&msg=ID nao fornecido");
    exit();
}

$contatoID = $_GET['id'];
$usuarioID = $_SESSION['id_usuario']; // ID do usuário logado

// --- 2. Deletar do Banco (Tabela 'ContatosEmergencia') ---
try {
    // A query só deleta se o ContatoID E o UsuarioID baterem (segurança)
    $sql = "DELETE FROM ContatosEmergencia 
            WHERE ContatoID = :contatoID AND UsuarioID = :usuarioID";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contatoID', $contatoID, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
         throw new Exception("Nao foi possivel remover (contato nao encontrado ou permissao negada).");
    }

    // --- 3. Redirecionar com Sucesso ---
    header("Location: ../paginas/cadastrar-contato.php?status=deletado");
    exit();

} catch (Exception $e) {
    // --- 4. Redirecionar com Erro ---
    $erroMsg = urlencode("Erro ao remover: " . $e->getMessage());
    header("Location: ../paginas/cadastrar-contato.php?status=erro&msg={$erroMsg}");
    exit();
}
?>