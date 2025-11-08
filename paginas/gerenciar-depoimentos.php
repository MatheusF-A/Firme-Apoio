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

// 4. Lógica para mensagens de status ( vindas do approve/delete)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

// 5. Buscar os depoimentos PENDENTES (aprovado = 0)
try {
    // Faz o JOIN com a tabela 'usuario' para pegar o nome
    $sql = "SELECT 
                d.depoimentoID, 
                d.titulo, 
                d.texto, 
                d.usuarioID,
                u.nome as autorNome
            FROM depoimentos d
            JOIN usuario u ON d.usuarioID = u.usuarioID
            WHERE d.aprovado = 0 
            ORDER BY d.depoimentoID ASC"; // Mais antigos primeiro
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $depoimentos_pendentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $depoimentos_pendentes = [];
    $erro = "Erro ao buscar depoimentos pendentes: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Depoimentos - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/gerenciar-depoimentos.css"> <!-- CSS Próprio -->
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
        
        <!-- Abas de Navegação -->
        <nav class="autocuidado-tabs">
            <a href="cadastrar-publicacao.php" class="tab-link">
                <i class="fas fa-book-open"></i> Publicações
            </a>
            <a href="cadastrar-video.php" class="tab-link">
                <i class="fas fa-video"></i> Vídeos
            </a>
            <a href="gerenciar-depoimentos.php" class="tab-link active">
                <i class="fas fa-comment-dots"></i> Depoimentos
            </a>
        </nav>

        <header class="content-header-admin">
            <h1>Moderação de Depoimentos</h1>
            <p>Revise os depoimentos enviados pelos usuários. Apenas depoimentos aprovados serão exibidos publicamente.</p>
        </header>

        <!-- Mensagens de Status (para delete, aprovação, etc.) -->
        <?php if ($status === 'aprovado'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Depoimento aprovado com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'deletado'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Depoimento deletado com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> Erro: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>


        <!-- Lista de Depoimentos Pendentes -->
        <section class="depoimento-list-admin">
            
            <?php if (isset($erro)): ?>
                <p><?php echo $erro; ?></p>
            <?php elseif (empty($depoimentos_pendentes)): ?>
                <p class="depoimento-vazio">Nenhum depoimento pendente para revisão. Bom trabalho!</p>
            <?php else: ?>
                <?php foreach ($depoimentos_pendentes as $depoimento): ?>
                    
                    <article class="depoimento-card-admin">
                        <header class="depoimento-author">
                            Enviado por: @<?php echo htmlspecialchars(strtolower(explode(' ', $depoimento['autorNome'])[0])); ?>#<?php echo $depoimento['usuarioID']; ?>
                        </header>
                        <div class="depoimento-body">
                            <h3><?php echo htmlspecialchars($depoimento['titulo']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($depoimento['texto'])); ?></p>
                        </div>
                        <footer class="admin-actions">
                            <a href="../controles/deletar-depoimentoADMbd.php?id=<?php echo $depoimento['depoimentoID']; ?>" 
                               class="btn-crud btn-deletar" 
                               onclick="return confirm('Tem certeza que deseja DELETAR este depoimento?\n\n<?php echo addslashes(htmlspecialchars($depoimento['titulo'])); ?>');">
                                <i class="fas fa-trash"></i> Deletar
                            </a>
                            <a href="../controles/aprovar-depoimentobd.php?id=<?php echo $depoimento['depoimentoID']; ?>" 
                               class="btn-crud btn-aprovar">
                                <i class="fas fa-check"></i> Aprovar
                            </a>
                        </footer>
                    </article>

                <?php endforeach; ?>
            <?php endif; ?>
            
        </section>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script>

</body>
</html>