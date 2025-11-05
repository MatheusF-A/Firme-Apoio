<?php
// 1. Inclui a conexão
require_once __DIR__ . "/../config/conexao.php";

// 2. Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 3. Coleta os dados do formulário
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $areaAtuacao = $_POST['areaAtuacao'];     
    $instituicao = $_POST['instituicao'];   
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $dtNascimento = $_POST['dtNascimento'];

    // 4. Validação simples
    if ($senha !== $confirmar_senha) {
        echo "<script>alert('As senhas não coincidem.'); window.history.back();</script>";
        exit();
    }

    // 5. Limpa formatação do CPF e Telefone
    $cpf = preg_replace('/\D/', '', $cpf);
    $telefone = preg_replace('/\D/', '', $telefone);

    try {
        // 6. Verifica duplicidade de Email na tabela 'voluntario'
        $sqlCheckEmail = "SELECT COUNT(*) FROM voluntario WHERE email = :email"; // 
        $stmtCheckEmail = $conn->prepare($sqlCheckEmail);
        $stmtCheckEmail->bindParam(':email', $email);
        $stmtCheckEmail->execute();
        if ($stmtCheckEmail->fetchColumn() > 0) {
            throw new Exception('Este email já está cadastrado.');
        }

        // 7. Verifica duplicidade de CPF na tabela 'voluntario'
        $sqlCheckCPF = "SELECT COUNT(*) FROM voluntario WHERE CPF = :cpf"; // 
        $stmtCheckCPF = $conn->prepare($sqlCheckCPF);
        $stmtCheckCPF->bindParam(':cpf', $cpf);
        $stmtCheckCPF->execute();
        if ($stmtCheckCPF->fetchColumn() > 0) {
            throw new Exception('Este CPF já está cadastrado.');
        }

        // 8. Criptografa a senha
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
        
        // 9. Insere na tabela 'voluntario' 
        $sql = "INSERT INTO voluntario (nome, CPF, email, telefone, endereco, dtNascimento, senha, areaAtuacao, instituicao) 
                VALUES (:nome, :cpf, :email, :telefone, :endereco, :dtNascimento, :senha, :areaAtuacao, :instituicao)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':telefone', $telefone);
        $stmt->bindParam(':endereco', $endereco);
        $stmt->bindParam(':dtNascimento', $dtNascimento);
        $stmt->bindParam(':senha', $hashedPassword);
        $stmt->bindParam(':areaAtuacao', $areaAtuacao);
        $stmt->bindParam(':instituicao', $instituicao);
        
        if (!$stmt->execute()) {
            throw new Exception('Erro ao cadastrar voluntário.');
        }

        // 10. Sucesso
        echo "<script>
                alert('Cadastro de voluntário realizado com sucesso! Você será redirecionado para o login.');
                window.location.href = 'index.php';
              </script>";
        exit();

    } catch (Exception $e) {
        // 11. Trata erros
        echo "<script>
                alert('Erro ao cadastrar: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }

} else {
    header("Location: ../paginas/cadastrar-voluntario.php");
    exit();
}
?>