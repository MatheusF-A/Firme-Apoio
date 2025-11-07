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

// 4. LÓGICA DE ALERTA (vinda do redirecionamento)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Publicação - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/cadastro-publicacao.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Exibe o alerta JS se houver status na URL -->
    <?php if ($status === 'sucesso'): ?>
        <script>alert('Publicação cadastrada com sucesso!');</script>
    <?php endif; ?>
    <?php if ($status === 'erro'): ?>
        <script>alert('Erro: <?php echo htmlspecialchars(addslashes(urldecode($msg))); ?>');</script>
    <?php endif; ?>

    <?php require_once '../includes/sidebar.php'; ?>

    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn"><i class="fas fa-bars"></i></button>
        <div class="mobile-logo"><img src="../assets/img/logoLado.png" alt="Firme Apoio Logo"></div>
    </header>

    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <!-- Abas de Navegação -->
        <nav class="autocuidado-tabs">
            <a href="cadastrar-publicacao.php" class="tab-link active">
                <i class="fas fa-book-open"></i> Publicações
            </a>
            <a href="cadastrar-video.php" class="tab-link">
                <i class="fas fa-video"></i> Vídeos
            </a>
            <a href="gerenciar-depoimentos.php" class="tab-link">
                <i class="fas fa-comment-dots"></i> Depoimentos
            </a>
        </nav>

        <!-- Formulário HTML Padrão (Sem IDs de submit) -->
        <form action="../controles/cadastrar-publicacaobd.php" method="POST" enctype="multipart/form-data" class="form-cadastro-publicacao">
            <div class="form-container">
                <div class="form-grid">
                    
                    <div class="form-col-imagem">
                        <label for="imagem" class="imagem-upload-label">
                            <div class="imagem-preview" id="imagem-preview">
                                <i class="fas fa-image default-icon"></i>
                                <span class="imagem-preview-text">Enviar Imagem</span>
                            </div>
                        </label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                    </div>

                    <div class="form-col-inputs">
                        <label for="titulo">Escreva um título</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Escreva um título" required>

                        <label for="subtitulo">Escreva um subtítulo</label>
                        <input type="text" id="subtitulo" name="subtitulo" placeholder="Escreva um subtítulo" required>
                        
                        <label for="autor">Escreva o nome do Autor</label>
                        <input type="text" id="autor" name="autor" required value="">
                    </div>
                </div>

                <div class="form-col-full">
                    <label for="link">Link da publicação (Opcional)</label>
                    <input type="url" id="link" name="link" placeholder="https://...">
                </div>

                <div class="form-col-full">
                    <label for="texto">Escreva seu texto</label>
                    <textarea id="texto" name="texto" rows="10" placeholder="Escreva seu texto..." required></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="conteudos.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn-enviar">
                    <i class="fas fa-paper-plane"></i> 
                    <span>Enviar</span>
                </button>
            </div>
        </form>
    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/cadastro-publicacao.js"></script>

</body>
</html>