<?php
// paginas/chat/chat.php
session_start();
require_once('../../config/conexao.php');

if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['perfil'])) {
    header("Location: ../../index.php");
    exit;
}

$usuarioID = $_SESSION['id_usuario'];
$perfil = $_SESSION['perfil'];

// Variáveis
$banido = false;
$aceitouTermo = false;
$meuNickname = '';

if ($perfil === 'usuario') {
    $stmt = $conn->prepare("SELECT termo_chat_aceito, chat_nickname, chat_banido FROM usuario WHERE usuarioID = :id");
    $stmt->bindParam(':id', $usuarioID);
    $stmt->execute();
    $d = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($d) {
        if ($d['chat_banido'] == 1) $banido = true;
        $aceitouTermo = ($d['termo_chat_aceito'] == 1);
        $meuNickname = $d['chat_nickname'];
    }
} elseif ($perfil === 'voluntario') {
    $stmt = $conn->prepare("SELECT termo_chat_aceito FROM voluntario WHERE voluntarioID = :id");
    $stmt->bindParam(':id', $usuarioID);
    $stmt->execute();
    $d = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($d) $aceitouTermo = ($d['termo_chat_aceito'] == 1);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat - Firme Apoio</title>
    <link rel="stylesheet" href="../../assets/css/tema.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/chat.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php $path = '../../'; include('../../includes/sidebar.php'); ?>

    <div class="main-content">
        <?php if ($banido): ?>
            <div class="banned-screen">
                <i class="fas fa-ban"></i>
                <h1>Acesso Suspenso</h1>
                <p>Você foi removido deste chat.</p>
                <a href="../dashboard_usuario.php" class="btn-voltar">Voltar</a>
            </div>
        <?php else: ?>
            <div class="chat-header">
                <div class="header-info">
                    <h1>Espaço de Convivência</h1>
                    <p>Troque experiências.</p>
                </div>
                
                <div class="header-actions">
                    <?php if ($perfil === 'voluntario'): ?>
                        <button onclick="abrirModalBanidos()" class="btn-ver-banidos">
                            <i class="fas fa-user-slash"></i> Banidos
                        </button>

                        <a href="log.php" class="btn-ver-log" title="Ver Histórico Completo">
                            <i class="fas fa-list-alt"></i> Log
                        </a>
                        
                    <?php endif; ?>

                    <div class="header-identity">
                        <?php if ($perfil === 'usuario' && $aceitouTermo): ?>
                            <span class="badge-nick"><i class="fas fa-mask"></i> <?php echo htmlspecialchars($meuNickname); ?></span>
                        <?php elseif ($perfil === 'voluntario'): ?>
                            <span class="badge-mod">MODERADOR</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="chat-wrapper <?php echo (!$aceitouTermo) ? 'blur-content' : ''; ?>">
                <div class="chat-window" id="chat-window"></div>
                <form id="form-chat" class="chat-input-area">
                    <input type="text" id="mensagem-input" placeholder="Digite..." autocomplete="off" <?php echo (!$aceitouTermo) ? 'disabled' : ''; ?>>
                    <button type="submit" id="btn-enviar" <?php echo (!$aceitouTermo) ? 'disabled' : ''; ?>><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php if (!$banido && !$aceitouTermo): ?>
    <div class="modal-overlay" id="modal-disclaimer">
        <div class="modal-box warning">
            <div class="modal-icon"><i class="fas fa-file-contract"></i></div>
            <h2>Termos de Uso</h2>
            <div class="modal-body text-left">
                <ul class="termos-lista">
                    <li><i class="fas fa-user-secret"></i> Identidade: <?php echo ($perfil === 'usuario') ? 'Foi gerado um apelido aleatório anônimo, para proteger sua identidade' : 'Você é um MODERADOR, Ao clicar sobre uma mensagem, você pode banir um usuário do chat.'; ?></li>
                    <li><i class="fas fa-save"></i> Rastreabilidade: Suas ações são registradas e salvas nos registros da sua conta.</li>
                    <li><i class="fas fa-gavel"></i> Regras: Ofensas geram banimento.</li>
                    <li><i class="fas fa-exclamation-circle"></i> Isenção: Plataforma não se responsabiliza por nada escrito neste chat.</li>
                </ul>
            </div>
            <div class="modal-actions">
                <a href="../../index.php" class="btn-cancel">Sair</a>
                <button id="btn-aceitar-termo" class="btn-confirm">Aceitar</button>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('btn-aceitar-termo').addEventListener('click', function() {
            fetch('../../controles/chat/aceitar-termo.php', { method: 'POST' })
            .then(r => r.json()).then(d => { if(d.status === 'success') window.location.reload(); });
        });
    </script>
    <?php endif; ?>

    <?php if ($perfil === 'voluntario'): ?>
    <div class="modal-overlay" id="modal-detalhes" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Gerenciar Usuário</h2>
                <button class="close-modal" onclick="fecharModalDetalhes()">&times;</button>
            </div>
            <div class="modal-body text-left" id="corpo-detalhes">
                <p>Carregando...</p>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="fecharModalDetalhes()">Cancelar</button>
                <button id="btn-confirmar-ban" class="btn-banir">BANIR USUÁRIO</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="modal-lista-banidos" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Usuários Banidos</h2>
                <button class="close-modal" onclick="fecharModalBanidos()">&times;</button>
            </div>
            <div class="modal-body" style="max-height:60vh; overflow-y:auto;">
                <ul id="lista-banidos-ul" class="lista-banidos-style">
                    <li>Carregando...</li>
                </ul>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="fecharModalBanidos()">Fechar</button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/contraste.js"></script>

    <?php if ($aceitouTermo && !$banido): ?>
    <script>
        // ROTAS GERAIS
        const ROTA_ENVIAR = '../../controles/chat/enviar-mensagem.php';
        const ROTA_LISTAR = '../../controles/chat/listar-mensagens.php';
        const ROTA_DADOS  = '../../controles/chat/obter-dados-usuario.php';
        const ROTA_BANIR  = '../../controles/chat/banir-usuario.php';
        
        // ROTAS NOVAS
        const ROTA_LISTAR_BANIDOS = '../../controles/chat/listar-banidos.php';
        const ROTA_DESBANIR       = '../../controles/chat/desbanir-usuario.php';
        
        const EH_VOLUNTARIO = <?php echo ($perfil === 'voluntario') ? 'true' : 'false'; ?>;
    </script>
    <script src="../../assets/js/chat.js"></script>
    <?php endif; ?>

</body>
</html>