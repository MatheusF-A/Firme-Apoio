<?php
session_start();
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php"; 

// 2. Proteção da Página
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../index.php"); 
    exit();
}

// 3. Define a página ativa
$paginaAtiva = 'ajuda-externa';

// 4. LÊ O STATUS DO REDIRECIONAMENTO (da URL)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro desconhecido.';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Local de Apoio - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/cadastro-local.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php require_once '../includes/sidebar.php'; ?>

    <header class="mobile-header">
        <button id="hamburger-btn" class="hamburger-btn">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-logo">
            <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
        </div>
    </header>

    <div id="overlay" class="overlay"></div>

    <main class="main-content">
        
        <header class="content-header">
            <h1>Cadastrar Local de Apoio</h1>
        </header>
        
        <?php if ($status === 'sucesso'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> 
                Local cadastrado com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> 
                Erro ao cadastrar: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>

        <!-- Formulário HTML Padrão (sem IDs de JS) -->
        <form action="../controles/cadastrar-localbd.php" method="POST" enctype="multipart/form-data" class="form-cadastro-local">
            <div class="form-container">
                <div class="form-grid">
                    
                    <div class="form-col-imagem">
                        <label for="imagem" class="imagem-upload-label">
                            <div class="imagem-preview" id="imagem-preview">
                                <i class="fas fa-image default-icon"></i>
                                <span class="imagem-preview-text">Enviar Imagem</span>
                            </div>
                        </label>
                        <input type="file" id="imagem" name="imagem" accept="image/*">
                    </div>

                    <div class="form-col-inputs">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" required>

                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="4" required></textarea>
                    </div>
                </div>

                <div class="form-col-full">
                    <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" required>
                </div>

                <div class="form-col-split">
                    <div>
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" required>
                    </div>
                    <div>
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email">
                    </div>
                </div>

                <div class="form-col-full">
                    <label for="horario">Horário de Funcionamento:</label>
                    <input type="text" id="horario" name="horario" required>
                </div>
            </div>

            <div class="form-actions">
                <a href="ajuda-externa.php" class="btn-voltar">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn-enviar">
                    <i class="fas fa-paper-plane"></i> 
                    <span>Enviar</span>
                </button>
            </div>
        </form>
    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/cadastro-local.js"></script> 
    <script src="../assets/js/contraste.js"></script>

</body>
</html>