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

// 4. Lógica para mensagens de status ( vindas do delete/edit)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

// 5. Buscar as publicações do banco de dados
try {
    // Busca os campos da Tabela 'publicacao' (Quadro 13)
    $sql = "SELECT id, titulo, subtitulo, autor, Imagem 
            FROM publicacao 
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $publicacoes = [];
    $erro = "Erro ao buscar publicações: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdos - Publicações - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/conteudo-publicacao.css"> <!-- CSS Próprio -->
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
        <div class="conteudos-header">
            <nav class="autocuidado-tabs">
                <a href="conteudo-publicacao.php" class="tab-link active">
                    <i class="fas fa-book-open"></i> Publicações
                </a>
                <a href="conteudo-videos.php" class="tab-link">
                    <i class="fas fa-video"></i> Vídeos
                </a>
                <a href="conteudo-depoimento.php" class="tab-link">
                    <i class="fas fa-comment-dots"></i> Depoimentos
                </a>
            </nav>
            
            <!-- Botão de Gerenciamento (SÓ PARA VOLUNTÁRIOS) -->
            <?php if ($perfil === 'voluntario'): ?>
                <a href="cadastrar-publicacao.php" class="btn-novo-item">
                    <i class="fas fa-plus"></i> Gerenciar
                </a>
            <?php endif; ?>
        </div>

        <?php if ($status === 'deletado'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Publicação deletada com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'editado'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Publicação atualizada com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> Erro: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>

        <!-- Lista de Publicações -->
        <section class="pub-list">
            
            <?php if (isset($erro)): ?>
                <p><?php echo $erro; ?></p>
            <?php elseif (empty($publicacoes)): ?>
                <p>Nenhuma publicação cadastrada no momento.</p>
            <?php else: ?>
                <?php foreach ($publicacoes as $pub): ?>
                    
                    <article class="pub-card">
                        <!-- Imagem (com lógica BLOB) -->
                        <div class="pub-image">
                            <?php
                            if (!empty($pub['Imagem'])) {
                                $imgData = base64_encode($pub['Imagem']);
                                echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="' . htmlspecialchars($pub['titulo']) . '">';
                            } else {
                                echo '<img src="../assets/img/placeholder-publicacao.png" alt="Publicação">';
                            }
                            ?>
                        </div>
                        
                        <!-- Detalhes da Publicação -->
                        <div class="pub-details">
                            <h3><?php echo htmlspecialchars($pub['titulo']); ?></h3>
                            <p class="subtitle"><?php echo htmlspecialchars($pub['subtitulo']); ?></p>
                            <p class="author">Autor: <?php echo htmlspecialchars($pub['autor']); ?></p>

                            <?php if ($perfil === 'voluntario'): ?>
                                <div class="pub-admin-actions">
                                    <a href="editar-publicacao.php?id=<?php echo $pub['id']; ?>" class="btn-crud btn-editar">
                                        <i class="fas fa-pen"></i> Editar
                                    </a>
                                    <a href="../controles/deletar-publicacaobd.php?id=<?php echo $pub['id']; ?>" class="btn-crud btn-deletar" 
                                       onclick="return confirm('Tem certeza que deseja deletar esta publicação?\n\n<?php echo addslashes(htmlspecialchars($pub['titulo'])); ?>');">
                                        <i class="fas fa-trash"></i> Deletar
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div>
                        
                        <!-- Botão Acessar -->
                        <a href="publicacao-detalhe.php?id=<?php echo $pub['id']; ?>" class="btn-acessar">
                            ACESSAR
                        </a>
                    </article>

                <?php endforeach; ?>
            <?php endif; ?>
            
        </section>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script>

</body>
</html>