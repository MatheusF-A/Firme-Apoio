<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Firme Apoio</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/img/logoFirmeApoio.png" alt="Firme Apoio Logo">
        </div>

        <form action="valida-login.php" method="POST">
            
            <label for="login">Email:</label>
            <input type="email" id="login" name="login" required>

            <div class="password-header">
                <label for="senha">Senha:</label>
                <a href="esqueci-senha.php" class="forgot-password">Esqueceu sua senha?</a>
            </div>
            <input type="password" id="senha" name="senha" required>

            <a href="cadastro.php" class="register-link">Ainda não possui uma conta? Cadastre-se aqui!</a>

            <button type="submit">ENTRAR</button>
        </form>
    </div>

    <div class="contrast-toggle">
        <button id="toggle-contrast" title="Alternar Contraste">
            <img src="assets/img/contrast.png" alt="Ícone de Contraste">
        </button>
    </div>

</body>
</html>