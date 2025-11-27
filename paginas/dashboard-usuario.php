<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// 1. Proteção da Página (Apenas Usuários)
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'voluntario') {
         header("Location: dashboard_voluntario.php"); 
         exit();
    }
    header("Location: index.php"); 
    exit();
}

// 2. Define a página ativa
$paginaAtiva = 'inicio';
$usuarioID = $_SESSION['id_usuario'];

// --- 3. LÓGICA DAS MÉTRICAS DINÂMICAS ---
$erro_metricas = null;
$diasNaPlataforma = 0;
$habitosTotal = 0;
$habitosConcluidos = 0;
$habitosPercent = 0;
$exerciciosTotal = 0;
$exerciciosConcluidos = 0;
$exerciciosPercent = 0;
$dataCadastro = 'hoje'; // Padrão

try {
    // 3a. Métrica 1: Dias na Plataforma
    $sqlDias = "SELECT dataCadastro FROM usuario WHERE usuarioID = :id";
    $stmtDias = $conn->prepare($sqlDias);
    $stmtDias->bindParam(':id', $usuarioID, PDO::PARAM_INT);
    $stmtDias->execute();
    $dataCadastro = $stmtDias->fetchColumn();
    
    if ($dataCadastro) {
        $dataCadastroObj = new DateTime($dataCadastro);
        $hoje = new DateTime();
        $diferenca = $hoje->diff($dataCadastroObj);
        $diasNaPlataforma = $diferenca->days;
    }

    // 3b. Métrica 2: Hábitos
    $sqlHabitos = "SELECT COUNT(*) as total, SUM(CASE WHEN concluido = 1 THEN 1 ELSE 0 END) as concluidos 
                   FROM habitos WHERE usuarioID = :id";
    $stmtHabitos = $conn->prepare($sqlHabitos);
    $stmtHabitos->bindParam(':id', $usuarioID, PDO::PARAM_INT);
    $stmtHabitos->execute();
    $habitosStats = $stmtHabitos->fetch(PDO::FETCH_ASSOC);
    
    if ($habitosStats) {
        $habitosTotal = (int)$habitosStats['total'];
        $habitosConcluidos = (int)$habitosStats['concluidos'];
        if ($habitosTotal > 0) {
            $habitosPercent = round(($habitosConcluidos / $habitosTotal) * 100);
        }
    }

    // 3c. Métrica 3: Exercícios
    $sqlExercicios = "SELECT COUNT(*) as total, SUM(CASE WHEN concluido = 1 THEN 1 ELSE 0 END) as concluidos 
                      FROM exercicios WHERE usuarioID = :id";
    $stmtExercicios = $conn->prepare($sqlExercicios);
    $stmtExercicios->bindParam(':id', $usuarioID, PDO::PARAM_INT);
    $stmtExercicios->execute();
    $exerciciosStats = $stmtExercicios->fetch(PDO::FETCH_ASSOC);

    if ($exerciciosStats) {
        $exerciciosTotal = (int)$exerciciosStats['total'];
        $exerciciosConcluidos = (int)$exerciciosStats['concluidos'];
        if ($exerciciosTotal > 0) {
            $exerciciosPercent = round(($exerciciosConcluidos / $exerciciosTotal) * 100);
        }
    }

    // 3d. Dados do Gráfico de Humor (Últimas 7 avaliações)
    $sqlHumorGrafico = "SELECT notaHumor, dataRealizacao 
                        FROM autoavaliacao 
                        WHERE usuarioID = :id
                        ORDER BY dataRealizacao DESC LIMIT 7";
    $stmtHumorGrafico = $conn->prepare($sqlHumorGrafico);
    $stmtHumorGrafico->bindParam(':id', $usuarioID, PDO::PARAM_INT);
    $stmtHumorGrafico->execute();
    
    $dadosHumorArray = array_reverse($stmtHumorGrafico->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    $erro_metricas = "Erro ao carregar dados: " . $e->getMessage();
}

// Prepara o array de dados para o JavaScript
$humorLabels = [];
$humorData = [];

foreach ($dadosHumorArray as $registro) {
    $humorLabels[] = date('d/m', strtotime($registro['dataRealizacao']));
    $humorData[] = $registro['notaHumor'];
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/tema.css"> 
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/dashboard-usuario.css"> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>
<body>

    <?php require_once '../includes/sidebar.php'; ?>

    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn"><i class="fas fa-bars"></i></button>
        <div class="mobile-logo"><img src="../assets/img/logoLado.png" alt="Firme Apoio Logo"></div>
    </header>

    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <!-- Boas-vindas e Cartões Principais (AGORA 4 CARDS) -->
        <section class="main-cards-grid">
            
            <!-- Card 1: Auto Cuidado -->
            <a href="auto-cuidado.php" class="big-card big-card-1">
                <i class="fas fa-heart"></i>
                <h2>AUTO CUIDADO</h2>
                <p>RECURSOS PARA O SEU BEM-ESTAR DIÁRIO</p>
                <div class="btn-acessar-simple">ACESSAR</div>
            </a>

            <!-- Card 2: Conteúdos -->
            <a href="conteudos.php" class="big-card big-card-2">
                <i class="fas fa-book-open"></i>
                <h2>CONTEÚDOS</h2>
                <p>ARTIGOS, VÍDEOS E DEPOIMENTOS DE APOIO</p>
                <div class="btn-acessar-simple">ACESSAR</div>
            </a>

            <!-- Card 3: Ajuda Externa -->
            <a href="ajuda-externa.php" class="big-card big-card-3">
                <i class="fas fa-map-marked-alt"></i>
                <h2>AJUDA EXTERNA</h2>
                <p>PROFISSIONAIS DISPONÍVEIS</p>
                <div class="btn-acessar-simple">ACESSAR</div>
            </a>
            
            <a href="cadastrar-contato.php" class="big-card big-card-4">
                <i class="fas fa-address-book"></i>
                <h2>CONTATOS DE EMERGÊNCIA</h2>
                <p>CADASTRE QUEM PODE TE AJUDAR</p>
                <div class="btn-acessar-simple">CADASTRAR</div>
            </a>

        </section>

        <!-- Métricas e Gráficos -->
        <section class="metrics-section">
            
            <!-- Métrica 1: Dias na Plataforma (Dinâmico) -->
            <div class="metric-card">
                <div class="metric-circle circle-1">
                    <span><?php echo $diasNaPlataforma; ?></span>
                </div>
                <h4>Dias na Plataforma</h4>
                <p>Jornada iniciada em <?php echo date('d/m/Y', strtotime($dataCadastro)); ?></p>
            </div>

            <!-- Métrica 2: Hábitos Concluídos (Dinâmico) -->
            <div class="metric-card">
                <div class="metric-circle circle-2" 
                     style="--progress-percent: <?php echo $habitosPercent; ?>;">
                    <span><?php echo $habitosPercent; ?>%</span>
                </div>
                <h4>Hábitos Concluídos</h4>
                <p><?php echo $habitosConcluidos; ?> de <?php echo $habitosTotal; ?> hábitos</p>
            </div>

            <!-- Métrica 3: Exercícios Concluídos (Dinâmico) -->
            <div class="metric-card">
                <div class="metric-circle circle-3"
                     style="--progress-percent: <?php echo $exerciciosPercent; ?>;">
                    <span><?php echo $exerciciosPercent; ?>%</span>
                </div>
                <h4>Exercícios Concluídos</h4>
                <p><?php echo $exerciciosConcluidos; ?> de <?php echo $exerciciosTotal; ?> atividades</p>
            </div>

        </section>

        <!-- Seu Panorama Emocional (Gráfico) -->
        <section class="panorama-section">
            <header>
                <h2>Seu Panorama Emocional (Últimos 7 Registros)</h2>
            </header>
            <div class="panorama-chart-container" id="panorama-chart-container">
                <canvas id="humorChart"></canvas>
            </div>
        </section>
        
        <!-- (Seção de Atividade Recente removida) -->
        
    </main>

    <!-- Passa os dados do humor para o JavaScript -->
    <script>
        const humorLabels = <?php echo json_encode($humorLabels); ?>;
        const humorData = <?php echo json_encode($humorData); ?>;
    </script>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 
    <script src="../assets/js/dashboard-usuario.js"></script>

</body>
</html>