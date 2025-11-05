<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario' || !isset($_SESSION['id_usuario'])) {
    header("Location: ../paginas/index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../paginas/habitos.php?status=erro&msg=ID nao fornecido");
    exit();
}

$habitoID = $_GET['id'];
$usuarioID = $_SESSION['id_usuario'];

// --- 2. Deletar do Banco ---
try {
    // A query só deleta se o habitoID E o usuarioID baterem
    $sql = "DELETE FROM habitos WHERE habitoID = :habitoID AND usuarioID = :usuarioID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':habitoID', $habitoID, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->execute();
    
    // Verifica se alguma linha foi realmente deletada (segurança)
    if ($stmt->rowCount() > 0) {
        header("Location: ../paginas/habitos.php?status=sucesso");
    } else {
        throw new Exception("Nenhum habito encontrado ou permissao negada.");
    }
    exit();

} catch (Exception $e) {
    $erroMsg = urlencode("Erro ao deletar: " . $e->getMessage());
    header("Location: ../paginas/habitos.php?status=erro&msg={$erroMsg}");
    exit();
}
?>