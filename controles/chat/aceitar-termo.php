<?php
// controles/chat/aceitar-termo.php
session_start();
require_once('../../config/conexao.php');
require_once('../../includes/gerar-nickname.php'); 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido']);
    exit;
}

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['perfil'])) {
    echo json_encode(['status' => 'error', 'message' => 'Não autorizado']);
    exit;
}

$id = $_SESSION['id_usuario'];
$perfil = $_SESSION['perfil'];

try {
    if ($perfil === 'usuario') {
        // Lógica para Usuário: Gera Nickname + Aceite
        $novoNick = gerarNicknameAleatorio();
        $sql = "UPDATE usuario SET termo_chat_aceito = 1, chat_nickname = :nick WHERE usuarioID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nick', $novoNick);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

    } elseif ($perfil === 'voluntario') {
        // Lógica para Voluntário: Apenas Aceite (Sem Nickname, pois é Moderador)
        $sql = "UPDATE voluntario SET termo_chat_aceito = 1 WHERE voluntarioID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
    
    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro DB: ' . $e->getMessage()]);
}
?>