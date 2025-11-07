<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página (Apenas Voluntários)
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../index.php"); 
    exit();
}

// 3. Define a página ativa
$paginaAtiva = 'conteudos';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Vídeo - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/cadastro-video.css"> <!-- CSS Próprio -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php require_once '../includes/sidebar.php'; ?>

    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-logo">
            <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
        </div>
    </header>

    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <!-- Abas de Navegação (do novo design) -->
        <nav class="autocuidado-tabs">
            <a href="cadastrar-publicacao.php" class="tab-link">
                <i class="fas fa-book-open"></i> Publicações
            </a>
            <a href="cadastrar-video.php" class="tab-link active">
                <i class="fas fa-video"></i> Vídeos
            </a>
            <a href="gerenciar-depoimentos.php" class="tab-link">
                <i class="fas fa-comment-dots"></i> Depoimentos
            </a>
        </nav>

        <!-- Formulário Padrão AJAX (com IDs) -->
        <form action="../controles/cadastrar-videobd.php" method="POST" class="form-cadastro-video" id="form-cadastro-video">
            <div class="form-container">
                
                <div class="form-col-full">
                    <label for="titulo">Escreva um título</label>
                    <input type="text" id="titulo" name="titulo" placeholder="Escreva um título" required>
                </div>

                <div class="form-col-full">
                    <label for="url_video">Insira o link do vídeo</label>
                    <input type="url" id="url_video" name="url_video" required placeholder="https://www.youtube.com/watch?v=...">
                </div>

                <!-- (Campo 'Autor' removido conforme solicitado) -->

                <!-- Preview da thumbnail (layout do novo design) -->
                <div class="video-preview-layout">
                    <div class="video-thumb-box">
                        <img id="video-thumb" src="" alt="Preview do vídeo">
                        <div id="thumb-loading" class="thumb-overlay-text" style="display:none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                        <div id="thumb-default-text" class="thumb-overlay-text">
                            <i class="fas fa-play-circle"></i>
                            <span>Pré-Visualização</span>
                        </div>
                    </div>
                    <div class="video-preview-text">
                        <h3 id="preview-title">TITULO DO VÍDEO</h3>
                        <!-- (Info do Autor removida) -->
                    </div>
                </div>

                <!-- hidden com o video id (preenchido pelo JS) -->
                <input type="hidden" id="video_id" name="video_id" value="">

            </div>

            <div class="form-actions">
                <a href="conteudo-videos.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn-enviar" id="btn-submit-video">
                    <i class="fas fa-paper-plane"></i> 
                    <span id="btn-submit-text">Enviar</span>
                </button>
            </div>
        </form>
    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/cadastro-video.js"></script>

</body>
</html>