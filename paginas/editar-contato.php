<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// 1. Proteção e Captura do ID
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: index.php"); 
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: cadastrar-contato.php?status=erro&msg=ID nao fornecido");
    exit();
}

$contatoID = $_GET['id'];
$usuarioID = $_SESSION['id_usuario'];
$paginaAtiva = 'inicio';
$erro = null;

// 2. Buscar Dados do Contato no Banco
try {
    $sql = "SELECT Nome, telefone FROM ContatosEmergencia 
            WHERE ContatoID = :contatoID AND UsuarioID = :usuarioID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':contatoID', $contatoID, PDO::PARAM_INT);
    $stmt->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmt->execute();
    $contato = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$contato) {
        throw new Exception("Contato nao encontrado ou permissao negada.");
    }
} catch (Exception $e) {
    $erroMsg = urlencode("Erro ao buscar contato: " . $e->getMessage());
    header("Location: cadastrar-contato.php?status=erro&msg={$erroMsg}");
    exit();
}

// 3. Lógica de Alerta de Erro (se o 'editar-contatobd.php' falhar)
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro desconhecido.';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Contato - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/tema.css"> 
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <!-- Reutiliza o CSS da página de cadastro -->
    <link rel="stylesheet" href="../assets/css/cadastrar-contato.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
        
        <div class="contatos-container">
            <a href="cadastrar-contato.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar</a>
            
            <h1>Editar Contato de Emergência</h1>
            <p>Atualize as informações do seu contato de confiança.</p>
            
            <!-- Formulário de Edição (POST/Redirect) -->
            <form action="../controles/editar-contatobd.php" method="POST" class="contato-form">
                
                <!-- ID Oculto (MUITO IMPORTANTE) -->
                <input type="hidden" name="contatoID" value="<?php echo $contatoID; ?>">
                
                <div class="form-inputs">
                    <input type="text" name="nome" placeholder="Nome do Contato" required 
                           value="<?php echo htmlspecialchars($contato['Nome']); ?>">
                    <input type="tel" name="telefone" placeholder="Telefone (ex: (11) 9....)" required
                           value="<?php echo htmlspecialchars($contato['telefone']); ?>">
                </div>
                <button type="submit" class="btn-adicionar" style="background-color: var(--cor-btn-edit);">
                    <i class="fas fa-save"></i> Atualizar
                </button>
            </form>
        </div>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 

</body>
</html>