<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// 1. Proteção da Página (Apenas Voluntários)
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../index.php"); 
    exit();
}

// 2. Define a página ativa (conforme sua regra)
$paginaAtiva = 'auto-cuidado';
$voluntarioID = $_SESSION['id_usuario'];

// 3. Lógica para mensagens de status
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

// 4. Buscar as duas listas de usuários
$meus_pacientes = [];
$usuarios_disponiveis = [];
$erro = null;

try {
    // 4a. Meus Pacientes (JOIN com 'acompanhamento')
    $sqlMeus = "SELECT u.usuarioID, u.nome, u.email 
                FROM usuario u
                JOIN acompanhamento a ON u.usuarioID = a.usuarioID
                WHERE a.voluntarioID = :voluntarioID
                ORDER BY u.nome";
    $stmtMeus = $conn->prepare($sqlMeus);
    $stmtMeus->bindParam(':voluntarioID', $voluntarioID, PDO::PARAM_INT);
    $stmtMeus->execute();
    $meus_pacientes = $stmtMeus->fetchAll(PDO::FETCH_ASSOC);

    // 4b. Usuários Disponíveis (LEFT JOIN para achar quem NÃO está em 'acompanhamento')
    $sqlDisp = "SELECT u.usuarioID, u.nome, u.email 
                FROM usuario u
                LEFT JOIN acompanhamento a ON u.usuarioID = a.usuarioID
                WHERE a.voluntarioID IS NULL
                ORDER BY u.nome";
    $stmtDisp = $conn->prepare($sqlDisp);
    $stmtDisp->execute();
    $usuarios_disponiveis = $stmtDisp->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $erro = "Erro ao buscar usuários: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhamentos - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/tema.css"> 
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/acompanhamento.css"> 
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
        
        <header class="content-header-admin">
            <h1>Gerenciar Acompanhamentos</h1>
            <p>Acompanhe seus pacientes ou selecione novos usuários para iniciar o acompanhamento.</p>
        </header>

        <!-- Mensagens de Status -->
        <?php if ($status === 'sucesso'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Ação realizada com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> Erro: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>
        <?php if ($erro): ?>
             <div class="alert-message error">
                <i class="fas fa-times-circle"></i> <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>

        <!-- Seção 1: Meus Acompanhamentos -->
        <div class="acompanhamento-container">
            <h2>Meus Pacientes</h2>
            <div class="lista-usuarios">
                <?php if (empty($meus_pacientes)): ?>
                    <p class="lista-vazia">Você ainda não está acompanhando nenhum usuário.</p>
                <?php else: ?>
                    <?php foreach ($meus_pacientes as $paciente): ?>
                        <article class="usuario-card">
                            <div class="usuario-info">
                                <i class="fas fa-user-check"></i>
                                <div>
                                    <h3><?php echo htmlspecialchars($paciente['nome']); ?></h3>
                                    <span><?php echo htmlspecialchars($paciente['email']); ?></span>
                                </div>
                            </div>
                            <div class="usuario-actions">
                                <a href="contatos-emergencia.php?id=<?php echo $paciente['usuarioID']; ?>" class="btn-crud btn-contatos">
                                    <i class="fas fa-address-book"></i> Contatos
                                </a>
                                
                                <a href="relatorio-usuario.php?id=<?php echo $paciente['usuarioID']; ?>" class="btn-crud btn-relatorio">
                                    <i class="fas fa-file-pdf"></i> Relatório
                                </a>
                                <a href="../controles/remover-acompanhamento.php?id=<?php echo $paciente['usuarioID']; ?>" class="btn-crud btn-remover" onclick="return confirm('Tem certeza que deseja liberar este paciente?');">
                                    <i class="fas fa-times"></i> Liberar
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seção 2: Usuários Disponíveis -->
        <div class="acompanhamento-container">
            <h2>Usuários Disponíveis</h2>
            <div class="lista-usuarios">
                <?php if (empty($usuarios_disponiveis)): ?>
                    <p class="lista-vazia">Não há novos usuários aguardando acompanhamento.</p>
                <?php else: ?>
                    <?php foreach ($usuarios_disponiveis as $usuario): ?>
                        <article class="usuario-card">
                            <div class="usuario-info">
                                <i class="fas fa-user-plus"></i>
                                <div>
                                    <h3><?php echo htmlspecialchars($usuario['nome']); ?></h3>
                                    <span><?php echo htmlspecialchars($usuario['email']); ?></span>
                                </div>
                            </div>
                            <div class="usuario-actions">
                                <a href="../controles/adicionar-acompanhamento.php?id=<?php echo $usuario['usuarioID']; ?>" class="btn-crud btn-adicionar">
                                    <i class="fas fa-plus"></i> Acompanhar
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 

</body>
</html>