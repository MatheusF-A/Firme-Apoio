<?php
session_start();

//DEPURACAO
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . "/conexao/conexao.php";

if (isset($_POST['login']) && isset($_POST['senha'])) {
    $login = $_POST['login'];
    $senha = $_POST['senha'];
 
    // DEPURANDO
    //  echo "<pre>";
    //  print_r($_POST);
    //  echo "</pre>";
    // Consulta SQL para verificar as credenciais
    $query = "SELECT * FROM usuario WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $login);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o usuário foi encontrado
    if ($result) {
        // echo "Usuário encontrado. Verificando a senha...<br>"; // Mensagem de depuração

        // Debugando a senha fornecida e o hash no banco
        // echo "Senha fornecida: " . $senha . "<br>";
        // echo "Hash no banco: " . $result['senha'] . "<br>";

        // Verifica se a senha está correta
        if (password_verify($senha, $result['senha'])) {

            // DEPURANDO
            // Senha correta, inicia a sessão
            // echo "Senha correta. Iniciando a sessão...<br>"; // Mensagem de depuração
            $_SESSION['usuario'] = $result['nome'];
            $_SESSION['perfil'] = $result['perfil'];
            $_SESSION['id_usuario'] = $result['id'];
            $_SESSION['id_tg'] = $result['id_tg'];
            
            if (isset($result['cpf'])) {
                $_SESSION['cpf'] = $result['cpf'];
            }

            if ($result['perfil'] === 'aluno') {
                $queryAluno = "SELECT id_tg FROM aluno WHERE id_usuario = :id_usuario";
                $stmtAluno = $conn->prepare($queryAluno);
                $stmtAluno->bindParam(':id_usuario', $result['id']);
                $stmtAluno->execute();
                $aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC);

                if ($aluno && isset($aluno['id_tg'])) {
                    $_SESSION['id_tg'] = $aluno['id_tg'];
                }
            }

            switch ($result['perfil']) {
                case 'aluno':
                    header("Location: ./aluno/dashboard_aluno.php");
                    break;
                case 'professor':
                    header("Location: ./professor/dashboard_professor.php");
                    break;
                case 'coordenador':
                    header("Location: ./coordenador/dashboard_coordenador.php");
                    break;
                default:
                    echo "Perfil desconhecido.";
                    exit();
            }
            exit();
        } else {
            echo "Senha incorreta.<br>"; // Mensagem de depuração
        }
    } else {
        echo "Usuário não encontrado.<br>"; // Mensagem de depuração
    }
} else {
    echo "Por favor, preencha todos os campos.";
}
?>
