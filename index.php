<?php
include('conexao.php');
session_start();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE login = '$login'";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            // Login correto
            $_SESSION['usuario'] = $usuario['login'];
            $_SESSION['cargo'] = $usuario['cargo'];

            // Redireciona conforme o cargo
            header("Location: inicio.php");
            exit;
        } else {
            // Senha incorreta
            $mensagem = "Senha incorreta!";
        }
    } else {
        // Usuário não encontrado
        $mensagem = "Usuário não encontrado!";
    }
}
?>

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
            <a href="cadastrar.php"><button class="btn-voltar">Cadastrar</button></a>
        </div>

        <div class="right-panel">
            <h2>Login</h2>

            <!-- Exibir mensagem de erro, se houver -->
            <?php if (!empty($mensagem)): ?>
                <div class="alert erro"><?php echo $mensagem; ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="text" name="login" placeholder="Login" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" class="btn-cadastrar">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>