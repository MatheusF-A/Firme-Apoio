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

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">         
    <link rel="stylesheet" href="../assets/css/dashboard-voluntario.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Inclui o componente sidebar -->
    <?php require_once '../includes/sidebar.php'; ?>

    <!-- Header para Mobile (Versão única, sem duplicatas) -->
    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-logo">
            <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
        </div>
    </header>

    <!-- Overlay para fechar o menu (Versão única, sem duplicatas) -->
    <div id="overlay" class="overlay"></div>

    <!-- Conteúdo Principal -->
    <main class="main-content">
        
        <!-- Header de Boas-Vindas -->
        <header class="dashboard-header">
            <h1>Painel do Voluntário</h1>
            <p>Bem-vindo(a) de volta, <?php echo htmlspecialchars($nomeVoluntario); ?>! Selecione um atalho abaixo.</p>
        </header>

        <!-- ---- GRID DE CARDS ATUALIZADA ---- -->
        <section class="action-grid">
            
            <!-- Card 1: Acompanhamento (Mantido) -->
            <a href="acompanhamento.php" class="action-card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <h3>Acompanhar Usuários</h3>
                <p>Iniciar acompanhamentos e gerar relatórios de progresso dos pacientes.</p>
            </a>
            
            <!-- Card 2: Cadastrar Publicação (Novo) -->
            <a href="cadastrar-publicacao.php" class="action-card">
                <div class="card-icon"><i class="fas fa-pen-to-square"></i></div>
                <h3>Cadastrar Publicação</h3>
                <p>Escrever e publicar novos artigos e materiais de leitura para os usuários.</p>
            </a>

            <!-- Card 3: Cadastrar Vídeo (Novo) -->
            <a href="cadastrar-video.php" class="action-card">
                <div class="card-icon"><i class="fas fa-video"></i></div>
                <h3>Cadastrar Vídeo</h3>
                <p>Adicionar novos vídeos do YouTube à plataforma de conteúdos.</p>
            </a>

            <!-- Card 4: Aprovar Depoimentos (Novo) -->
            <a href="gerenciar-depoimentos.php" class="action-card">
                <div class="card-icon"><i class="fas fa-check-square"></i></div>
                <h3>Aprovar Depoimentos</h3>
                <p>Moderar e aprovar os depoimentos enviados pelos usuários.</p>
            </a>

            <!-- Card 5: Cadastrar Local (Mantido) -->
            <a href="cadastrar-local.php" class="action-card">
                <div class="card-icon"><i class="fas fa-hospital"></i></div>
                <h3>Cadastrar Locais de Ajuda</h3>
                <p>Adicionar novas clínicas e locais de apoio ao mapa de ajuda externa.</p>
            </a>>
        </section>
    </main>
    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script>
</body>
</html>