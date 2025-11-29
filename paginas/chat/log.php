<?php
// paginas/chat/log.php
session_start();
require_once('../../config/conexao.php');

// 1. Segurança: Apenas Voluntários
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    // Se for usuário tentando entrar, joga pro início
    header("Location: ../../index.php");
    exit;
}

$paginaAtiva = 'log-chat'; // Para marcar na sidebar (se implementarmos CSS pra isso depois)

// 2. Consulta Completa (Audit Trail)
try {
    $sql = "SELECT 
                m.mensagemID, 
                m.mensagem, 
                m.dataEnvio, 
                u.Nome as nomeUsuario, 
                u.chat_nickname,
                v.nome as nomeVoluntario
            FROM chat_mensagens m
            LEFT JOIN usuario u ON m.usuarioID = u.usuarioID
            LEFT JOIN voluntario v ON m.voluntarioID = v.voluntarioID
            ORDER BY m.dataEnvio DESC"; // Do mais recente para o mais antigo
            
    $stmt = $conn->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $erro = "Erro ao carregar logs.";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log de Auditoria - Firme Apoio</title>
    
    <link rel="stylesheet" href="../../assets/css/tema.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/log.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <?php 
    $path = '../../'; // Ajuste de caminho para a sidebar funcionar
    include('../../includes/sidebar.php'); 
    ?>

    <div class="main-content">
        
        <div class="header-log">
            <h1><i class="fas fa-list-alt"></i> Registro Geral do Chat</h1>
            <p>Histórico completo com identificação real dos usuários para fins de moderação.</p>
        </div>

        <div class="table-container">
            <?php if (isset($erro)): ?>
                <div class="alert-erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <p>Nenhuma mensagem registrada.</p>
                </div>
            <?php else: ?>
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Autor Real (Cadastro)</th>
                            <th>Identidade Pública</th>
                            <th>Mensagem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $row): ?>
                            <?php 
                                // Lógica de Exibição
                                if (!empty($row['nomeVoluntario'])) {
                                    // É Voluntário
                                    $nomeReal = $row['nomeVoluntario'];
                                    $identidadePublica = '<span class="tag-mod">MODERADOR</span>';
                                    $classeLinha = 'row-mod';
                                } else {
                                    // É Usuário
                                    $nomeReal = $row['nomeUsuario'] ?? 'Usuário Deletado';
                                    $identidadePublica = $row['chat_nickname'] ?? '-';
                                    $classeLinha = 'row-user';
                                }
                                $data = date('d/m/Y H:i', strtotime($row['dataEnvio']));
                            ?>
                            <tr class="<?php echo $classeLinha; ?>">
                                <td class="col-data"><?php echo $data; ?></td>
                                <td class="col-real"><strong><?php echo htmlspecialchars($nomeReal); ?></strong></td>
                                <td class="col-nick"><?php echo $identidadePublica; ?></td>
                                <td class="col-msg"><?php echo htmlspecialchars($row['mensagem']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

    </div>

    <script src="../../assets/js/sidebar.js"></script>
    <script src="../../assets/js/contraste.js"></script>
</body>
</html>