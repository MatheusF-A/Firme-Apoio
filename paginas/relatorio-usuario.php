<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// 1. Proteção da Página (Apenas Voluntários)
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: index.php"); 
    exit();
}

// 2. Coleta do ID e Busca do Nome do Paciente (para exibição)
$pacienteNome = "Usuário Desconhecido";
$pacienteID = $_GET['id'] ?? null;

if ($pacienteID) {
    try {
        $sql = "SELECT nome FROM usuario WHERE usuarioID = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $pacienteID, PDO::PARAM_INT);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dados) {
            $pacienteNome = $dados['nome'];
        } else {
             header("Location: acompanhamento.php?status=erro&msg=Usuario nao encontrado");
             exit;
        }
    } catch (Exception $e) {
         header("Location: acompanhamento.php?status=erro&msg=Erro ao buscar nome do usuario");
         exit;
    }
} else {
    header("Location: acompanhamento.php?status=erro&msg=ID do usuario nao fornecido");
    exit;
}


// 3. Define a página ativa
$paginaAtiva = 'auto-cuidado';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Relatório - Firme Apoio</title>
    
    <!-- Carrega o Dicionário de Cores PRIMEIRO -->
    <link rel="stylesheet" href="../assets/css/tema.css"> 
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/relatorio.css"> <!-- CSS Próprio -->
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
        
        <div class="relatorio-card">
            <h1>Relatório de Acompanhamento</h1>
            <p class="paciente-nome">Paciente: <strong><?php echo htmlspecialchars($pacienteNome); ?></strong></p>
            
            <div class="warning-box">
                <i class="fas fa-exclamation-triangle"></i>
                <p>O relatório será gerado a partir dos dados de autoavaliação, hábitos e exercícios inseridos pelo paciente na plataforma até o momento da geração.</p>
            </div>

            <div class="relatorio-metodo">
                <h2>O que este relatório inclui?</h2>
                <ul>
                    <li><i class="fas fa-chart-line"></i> Média de Humor (1-5) nas autoavaliações.</li>
                    <li><i class="fas fa-list-ul"></i> Últimas 3 respostas escritas de reflexão.</li>
                    <li><i class="fas fa-tasks"></i> Status de conclusão de Hábitos e Exercícios.</li>
                </ul>
            </div>
            
            <div class="relatorio-actions">
                <a href="acompanhamento.php" class="btn-crud btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <a href="../controles/gerar-relatorio.php?id=<?php echo $pacienteID; ?>" target="_blank" class="btn-crud btn-gerar">
                    <i class="fas fa-file-pdf"></i> Gerar e Baixar PDF
                </a>
            </div>
        </div>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 

</body>
</html>