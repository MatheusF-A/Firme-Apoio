<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

// 1. Proteção da Página (Apenas Usuários)
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: index.php"); 
    exit();
}

$paginaAtiva = 'inicio'; // Fica destacado o "Início"
$usuarioID = $_SESSION['id_usuario'];

// 2. Lógica para mensagens de status
$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

// 3. Buscar contatos existentes (Tabela ContatosEmergencia [cite: 778-784])
$erro_lista = null;
$contatos = [];
try {
    $sql = "SELECT ContatoID, Nome, telefone FROM ContatosEmergencia WHERE UsuarioID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $usuarioID, PDO::PARAM_INT);
    $stmt->execute();
    $contatos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $erro_lista = "Erro ao buscar contatos: " . $e->getMessage();
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
    <link rel="stylesheet" href="../assets/css/cadastrar-contato.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <!-- Exibe o alerta JS se houver status na URL -->
    <?php if ($status === 'sucesso'): ?>
        <script>alert('Contato salvo com sucesso!');</script>
    <?php endif; ?>
    <?php if ($status === 'deletado'): ?>
        <script>alert('Contato removido com sucesso!');</script>
    <?php endif; ?>

    <?php if ($status === 'editado'): ?>
        <script>alert('Contato atualizado com sucesso!');</script>
    <?php endif; ?>
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
            <a href="dashboard-usuario.php" class="btn-voltar"><i class="fas fa-arrow-left"></i> Voltar ao Início</a>
            
            <h1>Meus Contatos de Emergência</h1>
            <p>Adicione pessoas de confiança que podem ser contatadas em caso de necessidade.</p>
            
            <!-- Formulário de Cadastro (Padrão POST/Redirect) -->
            <form action="../controles/cadastrar-contatobd.php" method="POST" class="contato-form">
                <div class="form-inputs">
                    <input type="text" name="nome" placeholder="Nome do Contato" required>
                    <input type="tel" name="telefone" placeholder="Telefone (ex: (11) 9....)" required>
                </div>
                <button type="submit" class="btn-adicionar">
                    <i class="fas fa-plus"></i> Adicionar
                </button>
            </form>

            <!-- Lista de Contatos Existentes -->
            <div class="lista-contatos-titulo">
                <h2>Contatos Salvos</h2>
            </div>
            <div class="lista-contatos">
                <?php if ($erro_lista): ?>
                    <p class="lista-vazia"><?php echo htmlspecialchars($erro_lista); ?></p>
                <?php elseif (empty($contatos)): ?>
                    <p class="lista-vazia">Nenhum contato cadastrado.</p>
                <?php else: ?>
                    <?php foreach ($contatos as $contato): ?>
                        <div class="contato-card">
                            <i class="fas fa-address-card contato-icon"></i>
                            <div class="contato-info">
                                <h3><?php echo htmlspecialchars($contato['Nome']); ?></h3>
                                <span><?php echo htmlspecialchars($contato['telefone']); ?></span>
                            </div>

                            <div class="contato-actions">
                                <a href="editar-contato.php?id=<?php echo $contato['ContatoID']; ?>" 
                                   class="btn-crud btn-editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="../controles/deletar-contatobd.php?id=<?php echo $contato['ContatoID']; ?>" 
                                   class="btn-crud btn-deletar"
                                   onclick="return confirm('Tem certeza que deseja remover este contato?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/contraste.js"></script> 

</body>
</html>