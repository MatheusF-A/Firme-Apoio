<?php
session_start();
require_once __DIR__ . "/../config/conexao.php"; 

if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'usuario') {
    header("Location: index.php"); 
    exit();
}

$paginaAtiva = 'auto-cuidado';
$usuarioID = $_SESSION['id_usuario'];

$status = $_GET['status'] ?? null;
$msg = $_GET['msg'] ?? 'Ocorreu um erro.';

$habitos = [];
$frequencias = [];
try {
    // 1. Buscar os hábitos
    $sqlHabitos = "SELECT habitoID, nome, detalhes, imagem, concluido, frequenciaID
                   FROM habitos 
                   WHERE usuarioID = :usuarioID 
                   ORDER BY concluido ASC, habitoID DESC";
    $stmtHabitos = $conn->prepare($sqlHabitos);
    $stmtHabitos->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtHabitos->execute();
    $habitos = $stmtHabitos->fetchAll(PDO::FETCH_ASSOC);

    // 2. Buscar as Frequências
    $sqlFreq = "SELECT frequenciaID, frequencia FROM frequencia ORDER BY frequenciaID";
    $stmtFreq = $conn->prepare($sqlFreq);
    $stmtFreq->execute();
    $frequencias = $stmtFreq->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $erro = "Erro ao buscar dados: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Hábitos - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/auto-cuidado.css"> 
    <link rel="stylesheet" href="../assets/css/habitos.css"> 
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
        
        <nav class="autocuidado-tabs">
            <a href="auto-cuidado.php" class="tab-link">
                <i class="fas fa-poll"></i> Análise
            </a>
            <a href="habitos.php" class="tab-link active">
                <i class="fas fa-calendar-check"></i> Hábitos
            </a>
            <a href="exercicios.php" class="tab-link">
                <i class="fas fa-heartbeat"></i> Exercícios
            </a>
        </nav>

        <!-- Mensagens de Status -->
        <?php if ($status === 'sucesso'): ?>
            <div class="alert-message success">
                <i class="fas fa-check-circle"></i> Ação realizada com sucesso!
            </div>
        <?php endif; ?>
        <?php if ($status === 'erro'): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> Erro: <?php echo htmlspecialchars(urldecode($msg)); ?>
            </div>
        <?php endif; ?>

        <div class="habito-container">
            <header class="habito-header">
                <h2>Seus Hábitos</h2>
                <button type="button" class="btn-novo-habito" id="btn-abrir-modal-habito">
                    <i class="fas fa-plus"></i> Novo Hábito
                </button>
            </header>
            
            <section class="habito-list">
                <?php if (isset($erro)): ?>
                    <p><?php echo $erro; ?></p>
                <?php elseif (empty($habitos)): ?>
                    <p class="habito-vazio">Você ainda não cadastrou nenhum hábito. Comece clicando em "Novo Hábito"!</p>
                <?php else: ?>
                    <?php foreach ($habitos as $habito): ?>
                        
                        <article class="habito-card <?php echo $habito['concluido'] ? 'concluido' : ''; ?>">
                            <div class="habito-imagem">
                                <?php
                                if (!empty($habito['imagem'])) {
                                    $imgData = base64_encode($habito['imagem']);
                                    echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="' . htmlspecialchars($habito['nome']) . '">';
                                } else {
                                    echo '<img src="../assets/img/placeholder-habito.png" alt="Hábito">';
                                }
                                ?>
                            </div>
                            <div class="habito-detalhes">
                                <h3><?php echo htmlspecialchars($habito['nome']); ?></h3>
                                <p><strong>Detalhes:</strong> <?php echo htmlspecialchars($habito['detalhes']); ?></p>
                            </div>
                            <div class="habito-actions">
                                
                                <?php if (!$habito['concluido']): ?>
                                    <a href="../controles/concluir-habitobd.php?id=<?php echo $habito['habitoID']; ?>" class="btn-habito btn-concluir" onclick="return confirm('Marcar este hábito como concluído?');">
                                        <i class="fas fa-check"></i> <span>Concluir</span>
                                    </a>
                                <?php else: ?>
                                    <a href="../controles/concluir-habitobd.php?id=<?php echo $habito['habitoID']; ?>&acao=desfazer" class="btn-habito btn-desfazer">
                                        <i class="fas fa-undo"></i> <span>Desfazer</span>
                                    </a>
                                <?php endif; ?>
                                
                                <button type="button" class="btn-habito btn-editar" 
                                    title="Editar"
                                    data-id="<?php echo $habito['habitoID']; ?>"
                                    data-nome="<?php echo htmlspecialchars($habito['nome']); ?>"
                                    data-detalhes="<?php echo htmlspecialchars($habito['detalhes']); ?>"
                                    data-frequencia-id="<?php echo $habito['frequenciaID']; ?>">
                                    <i class="fas fa-pen"></i>
                                </button>
                                
                                <a href="../controles/deletar-habitobd.php?id=<?php echo $habito['habitoID']; ?>" class="btn-habito btn-deletar" title="Deletar" onclick="return confirm('Tem certeza que deseja deletar este hábito?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <div class="modal-overlay" id="modal-novo-habito">
        <div class="modal-card">
            
            <form id="form-habito-modal" method="POST" enctype="multipart/form-data">
                <!-- O Título será alterado pelo JS -->
                <h3 id="modal-title">NOVO HÁBITO</h3>
                
                <!-- O ID do Hábito (para edição) ficará aqui -->
                <input type="hidden" id="modal-habitoID" name="habitoID" value="">
                
                <div class="modal-form-group">
                    <label for="modal-nome">Nome do Hábito:</label>
                    <input type="text" id="modal-nome" name="nome" required>
                </div>
                
                <div class="modal-form-group">
                    <label for="modal-detalhes">Detalhes:</label>
                    <input type="text" id="modal-detalhes" name="detalhes">
                </div>
                
                <div class="modal-form-group">
                    <label for="modal-frequencia">Frequência:</label>
                    <select id="modal-frequencia" name="frequenciaID" required>
                        <option value="">Selecione a frequência...</option>
                        <?php foreach ($frequencias as $freq): ?>
                            <option value="<?php echo $freq['frequenciaID']; ?>">
                                <?php echo htmlspecialchars($freq['frequencia']); ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (empty($frequencias)): ?>
                             <option value="" disabled>Nenhuma frequência cadastrada</option>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="modal-form-group">
                    <label>Adicione uma imagem (Opcional):</label>
                    <label for="modal-imagem" class="btn-upload-imagem">
                        <i class="fas fa-upload"></i>
                        <span id="modal-imagem-text">Escolher arquivo...</span>
                    </label>
                    <input type="file" id="modal-imagem" name="imagem" accept="image/*">
                </div>
                
                <div class="modal-actions">
                    <button type="button" class="btn-voltar" id="btn-voltar-modal">
                        <i class="fas fa-times"></i> Voltar
                    </button>
                    <button type="submit" class="btn-salvar" id="btn-salvar-modal">
                        <i class="fas fa-save"></i> 
                        <span id="btn-salvar-text">Salvar</span>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/habitos-modal.js"></script>
    <script src="../assets/js/contraste.js"></script>
    
</body>
</html>