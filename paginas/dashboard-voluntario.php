<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: index.php"); 
    exit();
}

// 3. Define a página ativa
$paginaAtiva = 'inicio';

// Pega o nome do voluntário
$nomeVoluntario = $_SESSION['nome'] ?? 'Voluntário(a)';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Voluntário - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">          
    <link rel="stylesheet" href="../assets/css/dashboard-voluntario.css"> 

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Inclui o componente sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>

    <!-- Header para Mobile -->
    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-logo">
            <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
        </div>
    </header>

    <!-- Overlay para fechar o menu em telas pequenas -->
    <div id="overlay" class="overlay"></div>
    <!-- Header para Mobile -->
    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-logo">
            <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
        </div>
    </header>

    <!-- Overlay para fechar o menu em telas pequenas -->
    <div id="overlay" class="overlay"></div>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        
        <!-- Header de Boas-Vindas -->
        <header class="dashboard-header">
            <h1>Painel do Voluntário</h1>
            <p>Bem-vindo(a) de volta, <?php echo htmlspecialchars($nomeVoluntario); ?>! Selecione uma ação abaixo.</p>
        </header>

        <!-- Grid de Cards de Ação -->
        <section class="action-grid">
            <!-- Card 1: Acompanhamento de Usuários -->
            <a href="acompanhamento.php" class="action-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Acompanhamento de Usuários</h3>
                <p>Revisar autoavaliações e progresso dos usuários que você acompanha.</p>
            </a>
            
            <!-- Card 2: Visualizar Locais de Ajuda -->
            <a href="ajuda-externa.php" class="action-card">
                <div class="card-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Visualizar Locais</h3>
                <p>Ver a lista de todas as clínicas e locais de apoio cadastrados.</p>
            </a>

            <!-- Card 3: Cadastrar Novo Local -->
            <a href="cadastrar-local.php" class="action-card">
                <div class="card-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3>Cadastrar Novo Local</h3>
                <p>Adicionar uma nova clínica ou local de apoio ao sistema.</p>
            </a>

            <!-- Card 4: Gerenciar Publicações (Baseado no TCC) -->
            <a href="gerenciar-publicacoes.php" class="action-card">
                <div class="card-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h3>Gerenciar Publicações</h3>
                <p>Escrever, editar e publicar artigos e materiais de leitura para os usuários.</p>
            </a>

            <!-- Card 5: Gerenciar Vídeos (Baseado no TCC) -->
            <a href="gerenciar-videos.php" class="action-card">
                <div class="card-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3>Gerenciar Vídeos</h3>
                <p>Adicionar ou remover vídeos de apoio da galeria de conteúdos.</p>
            </a>

            <!-- Card 6: Meu Perfil -->
            <a href="perfil-voluntario.php" class="action-card">
                <div class="card-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h3>Meu Perfil</h3>
                <p>Atualizar suas informações pessoais, área de atuação e senha.</p>
            </a>
        </section>

    </main>

    <script src="../assets/js/sidebar.js"></script>

</body>
</html>