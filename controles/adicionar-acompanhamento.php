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
$voluntarioID = $_SESSION['id_usuario'];

// --- 2. Inserir no Banco (Tabela 'acompanhamento') ---
try {
    // Verifica se já não está sendo acompanhado
    $sqlCheck = "SELECT * FROM acompanhamento WHERE usuarioID = :usuarioID";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtCheck->execute();
    
    if ($stmtCheck->rowCount() > 0) {
        throw new Exception("Este usuario ja esta sendo acompanhado por outro voluntario.");
    }

    // Se não estiver, insere
    $sql = "INSERT INTO acompanhamento (usuarioID, voluntarioID) VALUES (:usuarioID, :voluntarioID)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->bindParam(':voluntarioID', $voluntarioID, PDO::PARAM_INT);
    $stmt->execute();

    // --- 3. Redirecionar com Sucesso ---
    header("Location: ../paginas/acompanhamento.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // --- 4. Redirecionar com Erro ---
    $erroMsg = urlencode("Erro ao adicionar: " . $e->getMessage());
    header("Location: ../paginas/acompanhamento.php?status=erro&msg={$erroMsg}");
    exit();
}
?>