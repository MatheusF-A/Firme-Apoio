<?php
// controles/aceitar-termo.php
session_start();
require_once('../../config/conexao.php');
require_once('../../includes/gerar-nickname.php'); // Importante: Inclui o gerador

header('Content-Type: application/json');

// 1. Segurança
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Método inválido']);
    exit;
}

if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'usuario') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado']);
    exit;
}

$usuarioID = $_SESSION['id_usuario'];

try {
    // 2. Gera o Nickname AGORA (momento do aceite)
    $novoNick = gerarNicknameAleatorio();

    // 3. Atualiza o banco: Aceite = 1 E define o Nickname
    $sql = "UPDATE usuario 
            SET termo_chat_aceito = 1, 
                chat_nickname = :nick 
            WHERE usuarioID = :id";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nick', $novoNick);
    $stmt->bindParam(':id', $usuarioID, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'status' => 'success', 
        'message' => 'Bem-vindo ao chat! Sua identidade secreta é: ' . $novoNick,
        'nickname' => $novoNick
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erro no banco: ' . $e->getMessage()]);
}
?>