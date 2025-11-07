<?php
session_start();

// 1. Inclui a conexão
require_once __DIR__ . "./config/conexao.php";

// 2. Verifica se os dados foram enviados via POST
if (isset($_POST['login']) && isset($_POST['senha'])) {
    
    $email = $_POST['login'];
    $senha_postada = $_POST['senha'];

    try {
        // 3. TENTA LOGAR COMO 'USUARIO'
        $sqlUsuario = "SELECT * FROM usuario WHERE email = :email";
        $stmtUsuario = $conn->prepare($sqlUsuario);
        $stmtUsuario->bindParam(':email', $email);
        $stmtUsuario->execute();
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

        // 4. Verifica se encontrou um usuário e se a senha está correta
        if ($usuario && password_verify($senha_postada, $usuario['senha'])) {
            
            // 5. Inicia a sessão como 'usuario'
            $_SESSION['id_usuario'] = $usuario['usuarioID']; // [cite: 36]
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['perfil'] = 'usuario'; // Define o perfil

            // Redireciona para o dashboard do usuário
            header("Location: dashboard_usuario.php");
            exit();
        }

        // 6. SE NÃO FOR USUÁRIO, TENTA LOGAR COMO 'VOLUNTARIO'
        $sqlVoluntario = "SELECT * FROM voluntario WHERE email = :email";
        $stmtVoluntario = $conn->prepare($sqlVoluntario);
        $stmtVoluntario->bindParam(':email', $email);
        $stmtVoluntario->execute();
        $voluntario = $stmtVoluntario->fetch(PDO::FETCH_ASSOC);

        // 7. Verifica se encontrou um voluntário e se a senha está correta
        if ($voluntario && password_verify($senha_postada, $voluntario['senha'])) {
            
            // 8. Inicia a sessão como 'voluntario'
            $_SESSION['id_usuario'] = $voluntario['voluntarioID']; // [cite: 44]
            $_SESSION['nome'] = $voluntario['nome'];
            $_SESSION['perfil'] = 'voluntario'; // Define o perfil

            // Redireciona para o dashboard do voluntário
            header("Location: ./paginas/dashboard-voluntario.php");
            exit();
        }

        // 9. Se chegou até aqui, nenhum login foi válido
        echo "<script>
                alert('Email ou senha inválidos.');
                window.location.href = 'index.php';
              </script>";
        exit();

    } catch (Exception $e) {
        // 10. Trata erros de banco de dados
        echo "<script>
                alert('Erro ao processar o login: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }

} else {
    // Redireciona se não for POST
    echo "<script>
            alert('Por favor, preencha todos os campos.');
            window.location.href = 'index.php';
          </script>";
    exit();
}
?>