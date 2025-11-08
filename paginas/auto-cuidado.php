<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página (Apenas Usuários)
// Apenas 'usuario' pode ver e enviar esta página
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: index.php"); 
    exit();
}

// 3. Define a página ativa para a sidebar
$paginaAtiva = 'auto-cuidado';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Cuidado - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/auto-cuidado.css"> <!-- CSS desta página -->
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
        
        <!-- Navegação por Abas (Tabs) -->
        <nav class="autocuidado-tabs">
            <a href="auto-cuidado.php" class="tab-link active">
                <i class="fas fa-poll"></i> Análise
            </a>
            <a href="habitos.php" class="tab-link">
                <i class="fas fa-calendar-check"></i> Hábitos
            </a>
            <a href="exercicios.php" class="tab-link">
                <i class="fas fa-heartbeat"></i> Exercícios
            </a>
        </nav>

        <!-- Container da Avaliação -->
        <div class="avaliacao-container">
            <form id="form-auto-avaliacao" action="../controles/auto-avaliacaobd.php" method="POST">
                
                <h2>Como você está se sentindo hoje?</h2>
                
                <!-- Seleção de Humor (Radio Buttons Estilizados) -->
                <div class="mood-selector">
                    <input type="radio" id="humor-1" name="notaHumor" value="1" required>
                    <label for="humor-1" class="mood-emoji" title="Péssimo">😡</label>
                    
                    <input type="radio" id="humor-2" name="notaHumor" value="2">
                    <label for="humor-2" class="mood-emoji" title="Ruim">😞</label>
                    
                    <input type="radio" id="humor-3" name="notaHumor" value="3">
                    <label for="humor-3" class="mood-emoji" title="Normal">😐</label>
                    
                    <input type="radio" id="humor-4" name="notaHumor" value="4">
                    <label for="humor-4" class="mood-emoji" title="Bom">😊</label>
                    
                    <input type="radio" id="humor-5" name="notaHumor" value="5">
                    <label for="humor-5" class="mood-emoji" title="Ótimo">😄</label>
                </div>
                
                <h2>Poderia responder algumas perguntas?</h2>
                
                <div class="form-group">
                    <label for="pergunta1">1 - O que aconteceu com você?</label>
                    <small>Fale sobre uma situação (positiva/negativa) que aconteceu.</small>
                    <textarea id="pergunta1" name="perguntaUm" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="pergunta2">2 - O que você pensou ou sentiu sobre isso?</label>
                    <small>Digite como você agiu e se sentiu na hora da situação.</small>
                    <textarea id="pergunta2" name="perguntaDois" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="pergunta3">3 - Como você lidou com esta situação?</label>
                    <small>Conte como você agiu durante ou após a situação.</small>
                    <textarea id="pergunta3" name="perguntaTres" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn-enviar" id="btn-submit-avaliacao">
                    <span id="btn-submit-text">Enviar</span>
                </button>
                
            </form>
        </div>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/auto-cuidado.js"></script> 
    <script src="../assets/js/contraste.js"></script>

</body>
</html>