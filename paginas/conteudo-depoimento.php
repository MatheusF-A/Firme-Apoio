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
$perfil = $_SESSION['perfil'] ?? 'usuario';

// 4. Lógica para mensagens de status (vinda do delete)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

// 5. Buscar os depoimentos APROVADOS
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
            WHERE d.aprovado = 1 
            ORDER BY d.depoimentoID DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $depoimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $depoimentos = [];
    $erro = "Erro ao buscar depoimentos: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdos - Depoimentos - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/conteudo-depoimento.css"> <!-- CSS Próprio -->
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
            <a href="conteudo-publicacao.php" class="tab-link">
                <i class="fas fa-book-open"></i> Publicações
            </a>
            <a href="conteudo-videos.php" class="tab-link">
                <i class="fas fa-video"></i> Vídeos
            </a>
            <a href="conteudo-depoimento.php" class="tab-link active">
                <i class="fas fa-comment-dots"></i> Depoimentos
            </a>
        </nav>

        <!-- Botão Flutuante (depende do perfil) -->
        <?php if ($perfil === 'usuario'): ?>
            <a href="cadastrar-depoimento.php" class="btn-flutuante btn-gradiente">
                <i class="fas fa-pen-to-square"></i> Escrever Depoimento
            </a>
        <?php else: ?>
            <a href="gerenciar-depoimentos.php" class="btn-flutuante btn-gerenciar">
                <i class="fas fa-check-square"></i> Gerenciar Depoimentos
            </a>
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


        <!-- Lista de Depoimentos -->
        <section class="depoimento-list">
            
            <?php if (isset($erro)): ?>
                <p><?php echo $erro; ?></p>
            <?php elseif (empty($depoimentos)): ?>
                <p class="depoimento-vazio">Nenhum depoimento aprovado ainda. Seja o primeiro a escrever!</p>
            <?php else: ?>
                <?php foreach ($depoimentos as $depoimento): ?>
                    
                    <article class="depoimento-card">
                        
                        <?php if ($perfil === 'voluntario'): ?>
                            <a href="../controles/deletar-depoimentobd.php?id=<?php echo $depoimento['depoimentoID']; ?>" 
                               class="btn-deletar-item" 
                               title="Deletar Depoimento"
                               onclick="return confirm('Tem certeza que deseja deletar este depoimento?\n\n<?php echo addslashes(htmlspecialchars($depoimento['titulo'])); ?>');">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>
                        
                        <header class="depoimento-author">
                            @<?php echo htmlspecialchars(strtolower(explode(' ', $depoimento['autorNome'])[0])); ?>#<?php echo $depoimento['usuarioID']; ?>
                        </header>
                        <div class="depoimento-body">
                            <h3><?php echo htmlspecialchars($depoimento['titulo']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($depoimento['texto'])); ?></p>
                        </div>
                    </article>

                <?php endforeach; ?>
            <?php endif; ?>
            
        </section>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script>

</body>
</html>