<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Móveis Armazenamento</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <img id="logo" src="./img/logo.png" alt="Logo" class="logo">
            <h2>Seja bem-vindo</h2>
            <p>Ainda não tem uma conta?<br>Clique aqui embaixo e cadastre-se</p>
            <a href="cadastrar.php"><button class="btn-voltar">Voltar</button></a>
        </div>
        <div class="right-panel">
            <h2>Login</h2>
            <form action="verificar_login.php" method="POST">
                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert erro"><?php echo $_GET['erro']; ?></div>
                <?php endif; ?>

                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert sucesso"><?php echo $_GET['sucesso']; ?></div>
                <?php endif; ?>

                <input type="text" name="login" placeholder="Login" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" class="btn-cadastrar">Entrar</button>
            </form>
        </div>
    </div>
</body>

</html>