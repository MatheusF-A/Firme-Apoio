<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php"); 
    exit();
}

$paginaAtiva = 'desabafo';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desabafo - Em Breve - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/tema.css"> 
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/desabafo.css"> 
    
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
        
        <div class="dev-container">
            <i class="fas fa-tools"></i>
            <h1>Em Desenvolvimento</h1>
            <p>
                A funcionalidade "<strong>Desabafo / Chat em tempo real</strong>" é um dos trabalhos futuros 
                do nosso projeto e está sendo desenvolvido para uma próxima versão da plataforma.
            </p>
            <p>
                Obrigado pela sua compreensão!
            </p>
            
            <a href="javascript:history.back()" class="btn-voltar-dev">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 

</body>
</html>