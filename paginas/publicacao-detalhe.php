<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página (Todos logados podem ver)
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php"); 
    exit();
}

// 3. Define a página ativa
$paginaAtiva = 'conteudos';
$erro = null;
$pub = null;

// 4. Buscar a Publicação Específica
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $erro = "Nenhuma publicação selecionada.";
} else {
    try {
        $id = $_GET['id'];
        
        // Busca todos os campos da Tabela 'publicacao' (Quadro 13)
        $sql = "SELECT id, titulo, subtitulo, autor, link, texto, Imagem 
                FROM publicacao 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $pub = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pub) {
            $erro = "Publicação não encontrada.";
        }

    } catch (Exception $e) {
        $erro = "Erro ao buscar publicação: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- O título da página será dinâmico -->
    <title><?php echo $pub ? htmlspecialchars($pub['titulo']) : 'Publicação'; ?> - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/publicacao-detalhe.css"> <!-- CSS Próprio -->
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
        
        <div class="article-container">
            
            <!-- Botão de Voltar -->
            <a href="conteudo-publicacao.php" class="btn-voltar-pub">
                <i class="fas fa-arrow-left"></i> Voltar para Publicações
            </a>

            <?php if ($erro): ?>
                
                <!-- Se der erro ou não achar o ID -->
                <div class="pub-erro">
                    <h2><i class="fas fa-exclamation-triangle"></i> Erro</h2>
                    <p><?php echo htmlspecialchars($erro); ?></p>
                </div>

            <?php else: ?>

                <!-- Artigo Encontrado -->
                <article class="publicacao-content">
                    
                    <!-- 1. Cabeçalho do Artigo -->
                    <header class="pub-header">
                        <h1><?php echo htmlspecialchars($pub['titulo']); ?></h1>
                        <p class="subtitle"><?php echo htmlspecialchars($pub['subtitulo']); ?></p>
                        <p class="author">Por: <?php echo htmlspecialchars($pub['autor']); ?></p>
                    </header>
                    
                    <!-- 2. Imagem (se existir) -->
                    <?php if (!empty($pub['Imagem'])): ?>
                        <div class="pub-hero-image">
                            <?php
                            $imgData = base64_encode($pub['Imagem']);
                            echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="' . htmlspecialchars($pub['titulo']) . '">';
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- 3. Corpo do Texto -->
                    <div class="pub-body">
                        <?php
                        // nl2br() converte as quebras de linha (da textarea) em <br>
                        echo nl2br(htmlspecialchars($pub['texto'])); 
                        ?>
                        
                        <!-- 4. Link (se existir) -->
                        <?php if (!empty($pub['link'])): ?>
                            <p class="pub-link">
                                <strong>Fonte Original:</strong> 
                                <a href="<?php echo htmlspecialchars($pub['link']); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo htmlspecialchars($pub['link']); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                    </div>

                </article>

            <?php endif; ?>

        </div>
    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script>

</body>
</html>