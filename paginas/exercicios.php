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
$erro = null;

try {
    
    // ----> INÍCIO DO SCRIPT DE RESET AUTOMÁTICO <----
    
    // 1. Pega a data/hora atual DO SERVIDOR
    date_default_timezone_set('America/Sao_Paulo');
    $hoje = new DateTime();
    $hojeFormatado = $hoje->format('Y-m-d');
    $primeiroDiaDoMes = $hoje->format('Y-m-01');

    // 2. Prepara a query de reset
    $sqlReset = "
        UPDATE exercicios e
        JOIN frequencia f ON e.frequenciaID = f.frequenciaID
        SET 
            e.concluido = 0, 
            e.dataConclusao = NULL
        WHERE 
            e.usuarioID = :usuarioID 
            AND e.concluido = 1
            AND (
                -- Se for 'Diário' E a data de conclusão for ANTES de hoje
                (f.frequencia = 'Diário' AND e.dataConclusao < :hojeFormatado)
                
                -- Se for 'Mensal' E a data de conclusão for ANTES do dia 1 deste mês
                OR (f.frequencia = 'Mensal' AND e.dataConclusao < :primeiroDiaDoMes)
            )
    ";
    
    $stmtReset = $conn->prepare($sqlReset);
    $stmtReset->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtReset->bindParam(':hojeFormatado', $hojeFormatado);
    $stmtReset->bindParam(':primeiroDiaDoMes', $primeiroDiaDoMes);
    $stmtReset->execute();
    
    // ----> FIM DO SCRIPT DE RESET AUTOMÁTICO <----


    // 3. Buscar os exercícios (agora já estão atualizados)
    $exercicios = [];
    $frequencias = [];

    $sqlExercicios = "SELECT e.exercicioID, e.nome, e.detalhes, e.imagem, e.concluido, e.frequenciaID
                      FROM exercicios e
                      WHERE e.usuarioID = :usuarioID 
                      ORDER BY e.concluido ASC, e.exercicioID DESC";
    $stmtExercicios = $conn->prepare($sqlExercicios);
    $stmtExercicios->bindParam(':usuarioID', $usuarioID, PDO::PARAM_INT);
    $stmtExercicios->execute();
    $exercicios = $stmtExercicios->fetchAll(PDO::FETCH_ASSOC);

    // 4. Buscar as Frequências (para o modal)
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
    <title>Meus Exercícios - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/auto-cuidado.css"> 
    <link rel="stylesheet" href="../assets/css/exercicios.css"> 
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
            <a href="habitos.php" class="tab-link">
                <i class="fas fa-calendar-check"></i> Hábitos
            </a>
            <a href="exercicios.php" class="tab-link active">
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
         <?php if ($erro): ?>
            <div class="alert-message error">
                <i class="fas fa-times-circle"></i> <?php echo $erro; ?>
            </div>
        <?php endif; ?>

        <div class="exercicio-container">
            <header class="exercicio-header">
                <h2>Exercícios</h2>
                <button type="button" class="btn-novo-exercicio" id="btn-abrir-modal-exercicio">
                    <i class="fas fa-plus"></i> Novo Exercício
                </button>
            </header>
            
            <section class="exercicio-list">
                <?php if (empty($exercicios) && !$erro): ?>
                    <p class="exercicio-vazio">Você ainda não cadastrou nenhum exercício.</p>
                <?php else: ?>
                    <?php foreach ($exercicios as $exercicio): ?>
                        
                        <article class="exercicio-card <?php echo $exercicio['concluido'] ? 'concluido' : ''; ?>">
                            <div class="exercicio-imagem">
                                <?php
                                if (!empty($exercicio['imagem'])) {
                                    $imgData = base64_encode($exercicio['imagem']);
                                    echo '<img src="data:image/jpeg;base64,' . $imgData . '" alt="' . htmlspecialchars($exercicio['nome']) . '">';
                                } else {
                                    echo '<img src="../assets/img/placeholder-exercicio.png" alt="Exercício">';
                                }
                                ?>
                            </div>
                            <div class="exercicio-detalhes">
                                <h3><?php echo htmlspecialchars($exercicio['nome']); ?></h3>
                                <p><strong>Detalhes:</strong> <?php echo htmlspecialchars($exercicio['detalhes']); ?></p>
                            </div>
                            <div class="exercicio-actions">
                                
                                <?php if (!$exercicio['concluido']): ?>
                                    <a href="../controles/concluir-exerciciobd.php?id=<?php echo $exercicio['exercicioID']; ?>" class="btn-exercicio btn-concluir" onclick="return confirm('Marcar este exercício como concluído?');">
                                        <i class="fas fa-check"></i> <span>Concluir</span>
                                    </a>
                                <?php else: ?>
                                    <a href="../controles/concluir-exerciciobd.php?id=<?php echo $exercicio['exercicioID']; ?>&acao=desfazer" class="btn-exercicio btn-desfazer">
                                        <i class="fas fa-undo"></i> <span>Desfazer</span>
                                    </a>
                                <?php endif; ?>
                                
                                <button type="button" class="btn-exercicio btn-editar" 
                                    title="Editar"
                                    data-id="<?php echo $exercicio['exercicioID']; ?>"
                                    data-nome="<?php echo htmlspecialchars($exercicio['nome']); ?>"
                                    data-detalhes="<?php echo htmlspecialchars($exercicio['detalhes']); ?>"
                                    data-frequencia-id="<?php echo $exercicio['frequenciaID']; ?>">
                                    <i class="fas fa-pen"></i>
                                </button>
                                
                                <a href="../controles/deletar-exerciciobd.php?id=<?php echo $exercicio['exercicioID']; ?>" class="btn-exercicio btn-deletar" title="Deletar" onclick="return confirm('Tem certeza que deseja deletar este exercício?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <!-- Modal (Card) de Cadastro/Edição -->
    <div class="modal-overlay" id="modal-novo-exercicio">
        <div class="modal-card">
            
            <form id="form-exercicio-modal" method="POST" enctype="multipart/form-data">
                <h3 id="modal-title">NOVO EXERCÍCIO</h3>
                
                <input type="hidden" id="modal-exercicioID" name="exercicioID" value="">
                
                <div class="modal-form-group">
                    <label for="modal-nome">Nome do Exercício:</label>
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
    <!-- Fim do Modal -->

    <script src="../assets/js/sidebar.js"></script>
    <script src="../assets/js/exercicios-modal.js"></script>

</body>
</html>