<?php
// controles/chat/obter-dados-usuario.php
session_start();
require_once('../../config/conexao.php');
header('Content-Type: application/json; charset=utf-8');

// Segurança
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    echo json_encode(['status' => 'error', 'message' => 'Acesso negado']); 
    exit;
}

$id = $_POST['id_alvo'] ?? 0;

if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']); 
    exit;
}

try {

    $stmt = $conn->prepare("SELECT * FROM usuario WHERE usuarioID = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $dados = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dados) {

        $nomeReal = $dados['Nome'] ?? $dados['nome'] ?? 'Desconhecido';
        $email = $dados['email'] ?? $dados['Email'] ?? 'Sem email';
        $nick = $dados['chat_nickname'] ?? 'Sem nick';
        $dataBruta = $dados['dataCadastro'] ?? $dados['datacadastro'] ?? null;

        // Formata data se existir
        $dataF = $dataBruta ? date('d/m/Y', strtotime($dataBruta)) : '--/--/----';

        // Monta resposta limpa
        $resposta = [
            'Nome' => $nomeReal,
            'email' => $email,
            'chat_nickname' => $nick,
            'dtCadastro' => $dataF
        ];

        echo json_encode(['status' => 'success', 'dados' => $resposta]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Usuário não encontrado no banco (ID: ' . $id . ')']);
    }

} catch (Exception $e) {
    // Retorna o erro real do banco para sabermos o que é
    echo json_encode(['status' => 'error', 'message' => 'Erro SQL: ' . $e->getMessage()]);
}
?>