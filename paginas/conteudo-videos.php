<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php"); 
    exit();
}

$perfil = $_SESSION['perfil'] ?? 'usuario';
$paginaAtiva = 'conteudos';

// Função Helper
function extractYouTubeID($url) {
    if (!$url) return null;
    $patterns = [
        '/(?:youtube\.com\/.*v=|youtube\.com\/v\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/',
        '/(?:youtu\.be\/)([A-Za-z0-9_-]{11})/'
    ];
    foreach ($patterns as $re) {
        $m = preg_match($re, $url, $matches);
        if ($m && isset($matches[1])) return $matches[1];
    }
    return null;
}

// Buscar os vídeos
try {
    $sql = "SELECT id, titulo, link FROM videos ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $videos = [];
    $erro = "Erro ao buscar vídeos: " . $e->getMessage();
}

// As mensagens de status da URL foram removidas

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdos - Vídeos - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/conteudo-videos.css"> 
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
        
        <div class="conteudos-header">
            <nav class="autocuidado-tabs">
                <a href="conteudo-publicacao.php" class="tab-link">
                    <i class="fas fa-book-open"></i> Publicações
                </a>
                <a href="conteudos-videos.php" class="tab-link active">
                    <i class="fas fa-video"></i> Vídeos
                </a>
                <a href="gerenciar-depoimentos.php" class="tab-link">
                    <i class="fas fa-comment-dots"></i> Depoimentos
                </a>
            </nav>

            <?php if ($perfil === 'voluntario'): ?>
                <a href="cadastrar-video.php" class="btn-novo-item">
                    <i class="fas fa-plus"></i> Cadastrar Vídeo
                </a>
            <?php endif; ?>
        </div>
        <!-- Grid de Vídeos -->
        <section class="video-grid">
            
            <?php if (isset($erro)): ?>
                <p><?php echo $erro; ?></p>
            <?php elseif (empty($videos)): ?>
                <p>Nenhum vídeo cadastrado no momento.</p>
            <?php else: ?>
                <?php foreach ($videos as $video): ?>
                    <?php
                        $videoID_youtube = extractYouTubeID($video['link']);
                    ?>
                    <!-- O Card agora tem um ID único -->
                    <article class="video-card" id="video-card-<?php echo $video['id']; ?>">
                        
                        <?php if ($perfil === 'voluntario'): ?>
                            <button type="button" 
                               class="btn-deletar-item js-delete-video" 
                               title="Deletar Vídeo"
                               data-id="<?php echo $video['id']; ?>"
                               data-titulo="<?php echo htmlspecialchars(addslashes($video['titulo'])); ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php endif; ?>

                        <div class="video-player-wrapper">
                            <iframe 
                                src="https://www.youtube.com/embed/<?php echo $videoID_youtube; ?>" 
                                title="<?php echo htmlspecialchars($video['titulo']); ?>" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        
                        <div class="video-details">
                            <h3><?php echo htmlspecialchars($video['titulo']); ?></h3>
                            <div class="video-meta">
                                <span class="video-link">
                                    <i class="fab fa-youtube"></i> <a href="<?php echo htmlspecialchars($video['link']); ?>" target="_blank" rel="noopener noreferrer">Assistir no YouTube</a>
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
            
        </section>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/conteudo-videos.js"></script>

</body>
</html>