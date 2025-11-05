<?php
session_start();
require_once __DIR__ . "/../config/conexao.php";

// --- 1. Segurança ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../paginas/cadastrar-local.php");
    exit();
}
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'voluntario') {
    header("Location: ../paginas/index.php"); 
    exit();
}

// --- 2. Coleta de Dados (Texto) ---
$nome = $_POST['nome'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$endereco = $_POST['endereco'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$email = $_POST['email'] ?? '';
$horario = $_POST['horario'] ?? '';

// --- 3. Validação Simples (com Alerta JS) ---
if (empty($nome) || empty($descricao) || empty($endereco) || empty($telefone) || empty($horario)) {
    echo "<script>
            alert('Erro: Todos os campos (exceto email) são obrigatórios.'); 
            window.history.back(); 
          </script>";
    exit();
}

// --- 4. Processamento da Imagem ---
$imagem_blob = null;

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == UPLOAD_ERR_OK) {
    
    if ($_FILES['imagem']['size'] > 5 * 1024 * 1024) { 
        echo "<script>
                alert('Erro: A imagem é muito grande (Máx 5MB).'); 
                window.history.back(); 
              </script>";
        exit();
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_type = mime_content_type($_FILES['imagem']['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        echo "<script>
                alert('Erro: Tipo de arquivo inválido (Apenas JPEG, PNG, GIF).'); 
                window.history.back(); 
              </script>";
        exit();
    }

    $imagem_blob = file_get_contents($_FILES['imagem']['tmp_name']);
} 

// --- 5. Inserção no Banco de Dados ---
try {
    $sql = "INSERT INTO locaisajuda (nome, descricao, endereco, telefone, email, horario, imagem) 
            VALUES (:nome, :descricao, :endereco, :telefone, :email, :horario, :imagem)";
            
    $stmt = $conn->prepare($sql);
    
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':horario', $horario);
    $stmt->bindParam(':imagem', $imagem_blob, PDO::PARAM_LOB);
    
    $stmt->execute();

    // --- 6. Resposta de Sucesso (REDIRECIONAMENTO) ---
    header("Location: ../paginas/cadastrar-local.php?status=sucesso");
    exit();

} catch (Exception $e) {
    // --- 7. Resposta de Erro (REDIRECIONAMENTO) ---
    $erroMsg = urlencode("Erro de banco de dados. " . $e->getMessage());
    header("Location: ../paginas/cadastrar-local.php?status=erro&msg={$erroMsg}");
    exit();
}
?>