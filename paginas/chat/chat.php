<?php
// paginas/chat/chat.php
session_start();

// 1. Sobe 2 níveis para achar a conexão
require_once('../../config/conexao.php');

// 2. Segurança
if (!isset($_SESSION['id_usuario']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: ../../index.php");
    exit;
}

$usuarioID = $_SESSION['id_usuario'];
$paginaAtiva = 'chat'; 

// 3. Busca dados do usuário (Termo e Nickname)
$stmt = $conn->prepare("SELECT termo_chat_aceito, chat_nickname FROM usuario WHERE usuarioID = :id");
$stmt->bindParam(':id', $usuarioID);
$stmt->execute();
$userDados = $stmt->fetch(PDO::FETCH_ASSOC);

$aceitouTermo = ($userDados && $userDados['termo_chat_aceito'] == 1);
$meuNickname = $userDados['chat_nickname'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Coletivo - Firme Apoio</title>
    
    <link rel="stylesheet" href="../../assets/css/tema.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php 
    $path = '../../'; 
    include('../../includes/sidebar.php'); 
    ?>

    <div class="main-content">
        
        <div class="chat-header">
            <div class="header-info">
                <h1>Espaço de Convivência</h1>
                <p>Troque experiências com outros participantes.</p>
            </div>
            
            <?php if ($aceitouTermo && !empty($meuNickname)): ?>
            <div class="user-identity">
                <small>Você está visível como:</small>
                <span class="badge-nick"><i class="fas fa-mask"></i> <?php echo htmlspecialchars($meuNickname); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="chat-wrapper <?php echo !$aceitouTermo ? 'blur-content' : ''; ?>">
            
            <div class="chat-window" id="chat-window">
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <p>O chat está silencioso...</p>
                </div>
            </div>

            <form id="form-chat" class="chat-input-area">
                <input type="text" id="mensagem-input" placeholder="Digite sua mensagem..." autocomplete="off" <?php echo !$aceitouTermo ? 'disabled' : ''; ?>>
                <button type="submit" id="btn-enviar" <?php echo !$aceitouTermo ? 'disabled' : ''; ?>>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>

    <?php if (!$aceitouTermo): ?>
    <div class="modal-overlay" id="modal-disclaimer">
        <div class="modal-box warning">
            <div class="modal-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <h2>Atenção: Funcionalidade Opcional</h2>
            <div class="modal-body">
                <p>Você está prestes a entrar no <strong>Chat Coletivo</strong>. Por favor, leia com atenção:</p>
                <ul>
                    <li>Esta é uma funcionalidade <strong>experimental e opcional</strong>.</li>
                    <li>Sua identidade real será preservada publicamente.</li>
                    <li>A plataforma não se responsabiliza pelo conteúdo postado.</li>
                </ul>
            </div>
            <div class="modal-actions">
                <a href="../dashboard_usuario.php" class="btn-cancel">Não quero participar</a>
                <button id="btn-aceitar-termo" class="btn-confirm">Estou ciente e quero entrar</button>
            </div>
        </div>
    </div>
    <script src="../../assets/js/chat-disclaimer.js"></script>
    <?php endif; ?>

    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/contraste.js"></script>
    
    <?php if ($aceitouTermo): ?>
        <script>
            const ROTA_ENVIAR = '../../controles/chat/enviar-mensagem.php';
            const ROTA_LISTAR = '../../controles/chat/listar-mensagens.php';
        </script>
        <script src="../../assets/js/chat.js"></script>
    <?php endif; ?>

</body>
</html>