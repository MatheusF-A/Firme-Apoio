<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// 1. Proteção da Página e Coleta de IDs
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: acompanhamento.php?status=erro&msg=ID do usuario nao fornecido");
    exit();
}

$usuarioID = $_GET['id'];
$voluntarioID = $_SESSION['id_usuario'];
$paginaAtiva = 'auto-cuidado';
$erro = null;
$nomeUsuario = '';
$contatos = [];

try {
    // 2. VERIFICAÇÃO DE PERMISSÃO (CRUCIAL)
    $sqlPermissao = "SELECT * FROM acompanhamento 
                     WHERE usuarioID = :usuarioID AND voluntarioID = :voluntarioID";
    $stmtPermissao = $conn->prepare($sqlPermissao);
    $stmtPermissao->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtPermissao->bindParam(':voluntarioID', $voluntarioID, PDO::PARAM_INT);
    $stmtPermissao->execute();
    
    if ($stmtPermissao->rowCount() === 0) {
        throw new Exception("Você não tem permissão para ver os contatos deste usuário.");
    }

    // 3. Busca nome do usuário
    $sqlUsuario = "SELECT nome FROM usuario WHERE usuarioID = :usuarioID";
    $stmtUsuario = $conn->prepare($sqlUsuario);
    $stmtUsuario->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtUsuario->execute();
    $nomeUsuario = $stmtUsuario->fetchColumn();

    // 4. Busca os contatos de emergência (Tabela ContatosEmergencia)
    $sqlContatos = "SELECT Nome, telefone FROM ContatosEmergencia WHERE UsuarioID = :usuarioID";
    $stmtContatos = $conn->prepare($sqlContatos);
    $stmtContatos->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtContatos->execute();
    $contatos = $stmtContatos->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $erro = "Erro: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contatos de Emergência - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/tema.css"> 
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/contatos-emergencia.css"> <!-- CSS Próprio -->
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
        
        <div class="contatos-container">
            <a href="acompanhamento.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
            
            <h1>Contatos de Emergência</h1>
            <p class="paciente-nome">Paciente: <strong><?php echo htmlspecialchars($nomeUsuario); ?></strong></p>

            <?php if ($erro): ?>
                <div class="alert-message error">
                    <?php echo htmlspecialchars($erro); ?>
                </div>
            <?php elseif (empty($contatos)): ?>
                <p class="lista-vazia">Este usuário não cadastrou nenhum contato de emergência.</p>
            <?php else: ?>
                <div class="lista-contatos">
                    <?php foreach ($contatos as $contato): ?>
                        <div class="contato-card">
                            <i class="fas fa-address-card contato-icon"></i>
                            <div class="contato-info">
                                <h3><?php echo htmlspecialchars($contato['Nome']); ?></h3>
                                <span><?php echo htmlspecialchars($contato['telefone']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 

</body>
</html>