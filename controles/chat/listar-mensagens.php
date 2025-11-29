<?php
// controles/chat/listar-mensagens.php
session_start();
require_once('../../config/conexao.php');

// Cabeçalho JSON limpo
header('Content-Type: application/json; charset=utf-8');

$meuID = $_SESSION['id_usuario'] ?? 0;
$meuPerfil = $_SESSION['perfil'] ?? '';

try {
    // 1. QUERY SIMPLIFICADA
    // Trazemos apenas o necessário. O LEFT JOIN é só para pegar o nickname do usuário.
    // O voluntário não precisa de JOIN, pois o nome será fixo "MODERADOR".
    $sql = "SELECT 
                m.mensagemID, 
                m.mensagem, 
                m.dataEnvio, 
                m.usuarioID, 
                m.voluntarioID,
                u.chat_nickname
            FROM chat_mensagens m
            LEFT JOIN usuario u ON m.usuarioID = u.usuarioID
            ORDER BY m.mensagemID ASC";
            
    $stmt = $conn->query($sql);
    
    // Se der erro na consulta, retornamos array vazio para não travar a tela
    if (!$stmt) {
        echo json_encode([]);
        exit;
    }

    $mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $resultado = [];

    foreach ($mensagens as $msg) {
        
        // 2. Lógica: É Voluntário?
        $idVol = $msg['voluntarioID'];
        // Verifica se não é nulo e é maior que 0
        $ehVoluntario = (!empty($idVol) && $idVol > 0);

        // 3. Define Autor
        if ($ehVoluntario) {
            $autor = "MODERADOR";
            $isAdmin = true;
            $idReal = null; // Voluntário não é banível por aqui
        } else {
            // Se o nickname vier nulo (ex: usuário deletado), põe 'Anônimo'
            $autor = !empty($msg['chat_nickname']) ? $msg['chat_nickname'] : 'Anônimo';
            $isAdmin = false;
            $idReal = $msg['usuarioID'];
        }

        // 4. Sou Eu?
        $souEu = false;
        if ($meuPerfil === 'voluntario' && $ehVoluntario && $idVol == $meuID) {
            $souEu = true;
        } elseif ($meuPerfil === 'usuario' && !$ehVoluntario && $msg['usuarioID'] == $meuID) {
            $souEu = true;
        }

        // 5. Monta Objeto
        $resultado[] = [
            'id' => (int)$msg['mensagemID'],
            'texto' => htmlspecialchars($msg['mensagem']),
            'autor' => $autor,
            'hora' => date('H:i', strtotime($msg['dataEnvio'])),
            'sou_eu' => $souEu,
            'is_admin' => $isAdmin,
            'user_id_real' => $idReal
        ];
    }

    echo json_encode($resultado);

} catch (Exception $e) {
    // Em caso de catástrofe, retorna vazio para destravar o load
    echo json_encode([]);
}
?>