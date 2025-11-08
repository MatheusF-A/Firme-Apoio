<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página e Captura do ID
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: conteudo-publicacao.php?status=erro&msg=ID nao fornecido");
    exit();
}

$id = $_GET['id'];
$paginaAtiva = 'conteudos';

// 3. Buscar Dados da Publicação no Banco
try {
    $sql = "SELECT * FROM publicacao WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pub = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pub) {
        throw new Exception("Publicacao nao encontrada.");
    }
} catch (Exception $e) {
    $erroMsg = urlencode("Erro ao buscar publicacao: " . $e->getMessage());
    header("Location: conteudo-publicacao.php?status=erro&msg={$erroMsg}");
    exit();
}

// 4. Lógica de Alerta de Erro (se o 'editar-publicacaobd.php' falhar)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro desconhecido.';

// 5. Preparar Imagem de Preview (se existir)
$previewStyle = "";
if (!empty($pub['Imagem'])) {
    $imgData = base64_encode($pub['Imagem']);
    $previewStyle = "background-image: url('data:image/jpeg;base64,{$imgData}');";
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Publicação - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/cadastro-publicacao.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Estilo inline para o preview da imagem existente -->
    <style>
        #imagem-preview {
            <?php echo $previewStyle; ?>
        }
        <?php if (!empty($pub['Imagem'])): ?>
        #imagem-preview .default-icon,
        #imagem-preview .imagem-preview-text {
            display: none; 
        }
        <?php endif; ?>
    </style>
</head>
<body>

    <!-- Alerta de Erro (se o update falhar) -->
    <?php if ($status === 'erro'): ?>
        <script>alert('Erro: <?php echo htmlspecialchars(addslashes(urldecode($msg))); ?>');</script>
    <?php endif; ?>

    <?php require_once '../includes/sidebar.php'; ?>

    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn"><i class="fas fa-bars"></i></button>
        <div class="mobile-logo"><img src="../assets/img/logoLado.png" alt="Firme Apoio Logo"></div>
    </header>

    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <!-- Abas de Navegação -->
        <nav class="autocuidado-tabs">
            <a href="cadastrar-publicacao.php" class="tab-link active">
                <i class="fas fa-book-open"></i> Publicações
            </a>
            <a href="cadastrar-video.php" class="tab-link">
                <i class="fas fa-video"></i> Vídeos
            </a>
            <a href="gerenciar-depoimentos.php" class="tab-link">
                <i class="fas fa-comment-dots"></i> Depoimentos
            </a>
        </nav>

        <!-- Formulário de Edição -->
        <form action="../controles/editar-publicacaobd.php" method="POST" enctype="multipart/form-data" class="form-cadastro-publicacao">
            
            <!-- ID Oculto (MUITO IMPORTANTE) -->
            <input type="hidden" name="id" value="<?php echo $pub['id']; ?>">
            
            <div class="form-container">
                <div class="form-grid">
                    
                    <!-- Coluna da Imagem -->
                    <div class="form-col-imagem">
                        <label for="imagem" class="imagem-upload-label">
                            <div class="imagem-preview" id="imagem-preview">
                                <i class="fas fa-image default-icon"></i>
                                <span class="imagem-preview-text">Alterar Imagem</span>
                            </div>
                        </label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                    </div>

                    <!-- Coluna dos Inputs (Pré-preenchidos) -->
                    <div class="form-col-inputs">
                        <label for="titulo">Escreva um título</label>
                        <input type="text" id="titulo" name="titulo" required value="<?php echo htmlspecialchars($pub['titulo']); ?>">

                        <label for="subtitulo">Escreva um subtítulo</label>
                        <input type="text" id="subtitulo" name="subtitulo" required value="<?php echo htmlspecialchars($pub['subtitulo']); ?>">
                        
                        <label for="autor">Escreva o nome do Autor</label>
                        <input type="text" id="autor" name="autor" required value="<?php echo htmlspecialchars($pub['autor']); ?>">
                    </div>
                </div>

                <div class="form-col-full">
                    <label for="link">Link da publicação (Opcional)</label>
                    <input type="url" id="link" name="link" placeholder="https://..." value="<?php echo htmlspecialchars($pub['link']); ?>">
                </div>

                <div class="form-col-full">
                    <label for="texto">Escreva seu texto</label>
                    <textarea id="texto" name="texto" rows="10" placeholder="Escreva seu texto..." required><?php echo htmlspecialchars($pub['texto']); ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="conteudo-publicacao.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn-enviar">
                    <i class="fas fa-save"></i> 
                    <span>Atualizar</span>
                </button>
            </div>
        </form>
    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/cadastrar-publicacao.js"></script>
    <script src="../assets/js/contraste.js"></script>

</body>
</html>