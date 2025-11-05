<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// --- 1. Proteção e Captura do ID ---
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ajuda-externa.php?status=erro&msg=ID nao fornecido");
    exit();
}

$localID = $_GET['id'];
$paginaAtiva = 'ajuda-externa';

// --- 2. Buscar Dados do Local no Banco ---
try {
    $sql = "SELECT * FROM locaisajuda WHERE localID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $localID, PDO::PARAM_INT);
    $stmt->execute();
    $local = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$local) {
        throw new Exception("Local nao encontrado.");
    }
} catch (Exception $e) {
    $erroMsg = urlencode("Erro ao buscar local: " . $e->getMessage());
    header("Location: ajuda-externa.php?status=erro&msg={$erroMsg}");
    exit();
}

// --- 3. Lógica de Alerta de Erro (se houver) ---
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro desconhecido.';

// --- 4. Preparar Imagem de Preview (se existir) ---
$previewStyle = "";
if (!empty($local['imagem'])) {
    $imgData = base64_encode($local['imagem']);
    $previewStyle = "background-image: url('data:image/jpeg;base64,{$imgData}');";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Local de Apoio - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <!-- Reutilizando o CSS da página de cadastro -->
    <link rel="stylesheet" href="../assets/css/cadastro-local.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- NOVO: Estilo inline para o preview da imagem existente -->
    <style>
        #imagem-preview {
            <?php echo $previewStyle; ?>
        }
        <?php if (!empty($local['imagem'])): ?>
        #imagem-preview .default-icon,
        #imagem-preview .imagem-preview-text {
            display: none; /* Esconde o ícone/texto padrão se já houver imagem */
        }
        <?php endif; ?>
    </style>
</head>
<body>

    <?php require_once '../includes/sidebar.php'; ?>
    <header class="mobile-header"><button id="hamburger-btn" class="hamburger-btn"><i class="fas fa-bars"></i></button><div class="mobile-logo"><img src="../assets/img/logoLado.png" alt="Firme Apoio Logo"></div></header>
    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <header class="content-header">
            <h1>Editar Local de Apoio</h1>
        </header>
        
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> Erro: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>

        <!-- Formulário de Edição -->
        <form action="../controles/editar-localbd.php" method="POST" enctype="multipart/form-data" class="form-cadastro-local">
            
            <input type="hidden" name="localID" value="<?php echo $local['localID']; ?>">
            
            <div class="form-container">
                <div class="form-grid">
                    <div class="form-col-imagem">
                        <label for="imagem" class="imagem-upload-label">
                            <div class="imagem-preview" id="imagem-preview">
                                <i class="fas fa-image default-icon"></i>
                                <span class="imagem-preview-text">Alterar Imagem</span>
                            </div>
                        </label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                    </div>

                    <!-- Inputs pré-preenchidos -->
                    <div class="form-col-inputs">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($local['nome']); ?>">

                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($local['descricao']); ?></textarea>
                    </div>
                </div>

                <div class="form-col-full">
                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" required value="<?php echo htmlspecialchars($local['endereco']); ?>">
                </div>

                <div class="form-col-split">
                    <div>
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" required value="<?php echo htmlspecialchars($local['telefone']); ?>">
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($local['email']); ?>">
                    </div>
                </div>

                <div class="form-col-full">
                    <label for="horario">Horário de Funcionamento:</label>
                    <input type="text" id="horario" name="horario" required value="<?php echo htmlspecialchars($local['horario']); ?>">
                </div>
            </div>

            <div class="form-actions">
                <a href="ajuda-externa.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn-enviar">
                    <i class="fas fa-save"></i> Atualizar
                </button>
            </div>
        </form>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/cadastro-local.js"></script> 

</body>
</html>