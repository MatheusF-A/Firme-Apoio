<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario' || !isset($_SESSION['id_usuario'])) {
    header("Location: ../paginas/index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../paginas/habitos.php?status=erro&msg=ID do habito nao fornecido");
    exit();
}

// --- 3. Coleta de Dados ---
$habitoID = $_GET['id'];
$usuarioID = $_SESSION['id_usuario']; 
$acao = $_GET['acao'] ?? 'concluir'; 

// ----> INÍCIO DA MODIFICAÇÃO <----

// Define o novo status (1 = concluído, 0 = pendente)
$novoStatus = ($acao === 'concluir') ? 1 : 0;
// Se estiver concluindo, salva a data atual. Se estiver desfazendo, salva NULL.
$novaDataConclusao = ($acao === 'concluir') ? date('Y-m-d H:i:s') : null;

// --- 4. Atualização no Banco (UPDATE) ---
try {
    // A query agora atualiza 'concluido' E 'dataConclusao'
    $sql = "UPDATE habitos 
            SET concluido = :status, dataConclusao = :dataConclusao
            WHERE habitoID = :habitoID AND usuarioID = :usuarioID";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':status', $novoStatus, PDO::PARAM_INT);
    $stmt->bindParam(':dataConclusao', $novaDataConclusao); // Salva a data ou NULL
    $stmt->bindParam(':habitoID', $habitoID, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    
    $stmt->execute();
    
// ----> FIM DA MODIFICAÇÃO <----

    if ($stmt->rowCount() > 0) {
        header("Location: ../paginas/habitos.php?status=sucesso");
    } else {
        throw new Exception("Nenhum habito encontrado ou permissao negada.");
    }
    exit();

} catch (Exception $e) {
    $erroMsg = urlencode("Erro ao atualizar habito: ". $e->getMessage());
    header("Location: ../paginas/habitos.php?status=erro&msg={$erroMsg}");
    exit();
}
?>