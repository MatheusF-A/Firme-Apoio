<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ./index.php");
    exit();
}

// Busca os locais de ajuda
try {
    // ----> ATUALIZADO: Adicionado 'localID' ao SELECT
    $sql = "SELECT localID, nome, descricao, endereco, telefone, email, horario, imagem 
            FROM locaisajuda 
            ORDER BY nome";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $locais = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $locais = [];
    $erro = "Erro ao buscar locais de ajuda: " . $e->getMessage();
}

$paginaAtiva = 'ajuda-externa';

$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajuda Externa - Firme Apoio</title>
    
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/ajuda-externa.css">
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
        
        <header class="content-header">
            <h1>Clínicas e Suporte nas Proximidades</h1>
        </header>

        <?php if ($status === 'deletado'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Local deletado com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'editado'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Local atualizado com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> Erro: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>

        <section class="clinic-list">
            
            <?php if (isset($erro)): ?>
                <p><?php echo $erro; ?></p>
            <?php elseif (empty($locais)): ?>
                <p>Nenhum local de ajuda cadastrado no momento.</p>
            <?php else: ?>
                <?php foreach ($locais as $local): ?>
                    <article class="clinic-card">
                        <div class="clinic-image">
                            <?php
                            if (!empty($local['imagem'])) {
                                $imgData = base64_encode($local['imagem']);
                                echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="' . htmlspecialchars($local['nome']) . '">';
                            } else {
                                echo '<img src="../assets/img/placeholder-clinica.jpg" alt="Foto da clínica">';
                            }
                            ?>
                        </div> 
                        <div class="clinic-details">
                            <h3><?php echo htmlspecialchars($local['nome']); ?></h3>
                            <p class="subtitle"><?php echo htmlspecialchars($local['descricao']); ?></p>
                            
                            <p class="detail-item"><strong>Endereço:</strong> <?php echo htmlspecialchars($local['endereco']); ?></p>
                            <p class="detail-item"><strong>Telefone:</strong> <?php echo htmlspecialchars($local['telefone']); ?></p>
                            <p class="detail-item"><strong>Email:</strong> <?php echo htmlspecialchars($local['email']); ?></p>
                            <p class="detail-item"><strong>Horário:</strong> <?php echo htmlspecialchars($local['horario']); ?></p>
                            
                            <!--  BOTÕES DE ADMIN ---->
                            <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'voluntario'): ?>
                                <div class="clinic-admin-actions">
                                    <a href="editar-local.php?id=<?php echo $local['localID']; ?>" class="btn-crud btn-editar">
                                        <i class="fas fa-pen"></i> Editar
                                    </a>
                                    <a href="../controles/deletar-localbd.php?id=<?php echo $local['localID']; ?>" class="btn-crud btn-deletar" onclick="return confirm('Tem certeza que deseja deletar este local? (<?php echo addslashes(htmlspecialchars($local['nome'])); ?>)');">
                                        <i class="fas fa-trash"></i> Deletar
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <script src="../assets/js/sidebar.js"></script>

</body>
</html>