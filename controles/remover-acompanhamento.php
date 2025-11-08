<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../paginas/acompanhamento.php?status=erro&msg=ID do usuario nao fornecido");
    exit();
}

$usuarioID = $_GET['id'];
$voluntarioID = $_SESSION['id_usuario']; // Pega o ID do voluntário logado

// --- 2. Deletar do Banco (Tabela 'acompanhamento') ---
try {
    // A query só deleta se o voluntário logado for o 'dono' desse acompanhamento
    $sql = "DELETE FROM acompanhamento WHERE usuarioID = :usuarioID AND voluntarioID = :voluntarioID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->bindParam(':voluntarioID', $voluntarioID, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
         throw new Exception("Nao foi possivel remover (usuario nao encontrado ou permissao negada).");
    }

    // --- 3. Redirecionar com Sucesso ---
    header("Location: ../paginas/acompanhamento.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // --- 4. Redirecionar com Erro ---
    $erroMsg = urlencode("Erro ao remover: " . $e->getMessage());
    header("Location: ../paginas/acompanhamento.php?status=erro&msg={$erroMsg}");
    exit();
}
?>