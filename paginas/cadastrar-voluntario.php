<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Voluntário - Firme Apoio</title>

    <link rel="stylesheet" href="../assets/css/tema.css">
    <link rel="stylesheet" href="../assets/css/cadastro-form.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <div class="form-container">
        
        <div class="logo">
            <img src="../assets/img/logoLado.png" alt="Firme Apoio Logo">
        </div>

        <form action="../controles/cadastrar-voluntariobd.php" method="POST" id="cadastro-form">
            
            <div class="form-grid">
                <div class="form-column">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required> <label for="cpf">CPF:</label>
                    <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" maxlength="14" required> <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required> <label for="senha">Senha:</label>
                    <div class="input-group">
                        <input type="password" id="senha" name="senha" required> <span class="icon-toggle" id="toggle-senha"><i class="fa fa-eye"></i></span>
                    </div>
                    <p class="senha-erro" id="erro-senha"></p>

                    <label for="confirmar_senha">Confirme sua senha:</label>
                    <div class="input-group">
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required>
                        <span class="icon-toggle" id="toggle-confirmar-senha"><i class="fa fa-eye"></i></span>
                    </div>
                    <p class="senha-erro" id="erro-confirmar-senha"></p>
                </div>

                <div class="form-column">
                    <label for="areaAtuacao">Área de Atuação:</label>
                    <input type="text" id="areaAtuacao" name="areaAtuacao" required> <label for="instituicao">Instituição pertencente:</label>
                    <input type="text" id="instituicao" name="instituicao" required> <label for="telefone">Telefone:</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" maxlength="15" required> <label for="endereco">Endereço:</label>
                    <input type="text" id="endereco" name="endereco" required> <label for="dtNascimento">Data de Nascimento:</label>
                    <input type="date" id="dtNascimento" name="dtNascimento" required> </div>
            </div>

            <a href="../index.php" class="login-link">Já possui uma conta? Faça Login aqui!</a>

            <button type="submit" id="submit-btn">CADASTRAR-SE</button>
        </form>
    </div>

    <div class="contrast-toggle">
        <button id="toggle-contrast" title="Alternar Contraste">
            <img src="../assets/img/contrast.png" alt="Ícone de Contraste" id="contrast-icon-img-alt">
        </button>
    </div>

    <script src="../assets/js/contrastelogin.js"></script>
    <script src="../assets/js/cadastro-form.js" defer></script>


</body>
</html>