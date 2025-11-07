<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página (Todos logados podem ver)
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php"); 
    exit();
}

// 3. Define a página ativa e pega o perfil
$paginaAtiva = 'conteudos';
$perfil = $_SESSION['perfil'] ?? 'usuario'; // Padrão é 'usuario'

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdos - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/conteudos.css"> <!-- CSS Próprio -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php require_once '../includes/sidebar.php'; ?>

    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn"><i class="fas fa-bars"></i></button>
        <div class="mobile-logo"><img src="../assets/img/logoLado.png" alt="Firme Apoio Logo"></div>
    </header>

    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <header class="content-header">
            <h1>Central de Conteúdos</h1>
            <p>Selecione a área que deseja acessar.</p>
        </header>

        <!-- Grid de Cards de Ação -->
        <section class="choice-grid">

            <!-- --- CARDS DE VISUALIZAÇÃO (Para todos) --- -->

            <a href="conteudo-publicacao.php" class="choice-card">
                <div class="card-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Publicações</h3>
                <p>Ler artigos, guias e materiais de apoio escritos por nossos voluntários.</p>
            </a>
            
            <a href="conteudo-videos.php" class="choice-card">
                <div class="card-icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <h3>Vídeos</h3>
                <p>Assistir a vídeos motivacionais, palestras e conteúdos informativos.</p>
            </a>

            <a href="conteudo-depoimento.php" class="choice-card">
                <div class="card-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Depoimentos</h3>
                <p>Ler relatos e histórias de superação de outros membros da comunidade.</p>
            </a>


            <!-- --- CARDS DE GERENCIAMENTO (SÓ PARA VOLUNTÁRIOS) --- -->
            <?php if ($perfil === 'voluntario'): ?>
                
                <a href="cadastrar-publicacao.php" class="choice-card card-manage">
                    <div class="card-icon">
                        <i class="fas fa-pen-to-square"></i>
                    </div>
                    <h3>Cadastrar Publicações</h3>
                    <p>Criar e publicar artigos para os usuários.</p>
                </a>
                
                <a href="cadastrar-video.php" class="choice-card card-manage">
                    <div class="card-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Cadastrar Vídeos</h3>
                    <p>Adicionar vídeos para a galeria de conteúdos.</p>
                </a>

                <a href="gerenciar-depoimentos.php" class="choice-card card-manage">
                    <div class="card-icon">
                        <i class="fas fa-check-square"></i>
                    </div>
                    <h3>Revisar Depoimentos</h3>
                    <p>Revisar, aprovar ou remover depoimentos enviados.</p>
                </a>

            <?php endif; ?>

        </section>

    </main>

    <script src="../assets/js/sidebar.js"></script>

</body>
</html>