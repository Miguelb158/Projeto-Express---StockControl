<?php
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('conexao.php'); // conexão com o banco

    $cargo = $_POST['cargo'];
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    // validações básicas
    if (empty($cargo) || empty($login) || empty($senha)) {
        $mensagem = "Preencha todos os campos!";
    } else {
        // verifica se o login já existe
        $verifica = $conexao->prepare("SELECT * FROM usuarios WHERE login = ?");
        $verifica->bind_param("s", $login);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado->num_rows > 0) {
            $mensagem = "Usuário já cadastrado!";
        } else {
            // insere no banco
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = $conexao->prepare("INSERT INTO usuarios (cargo, login, senha) VALUES (?, ?, ?)");
            $sql->bind_param("sss", $cargo, $login, $senha_hash);

            if ($sql->execute()) {
                $mensagem = "Cadastro realizado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Móveis Armazenamento</title>
    <link rel="stylesheet" href="./css/login.css">
</head>

<body>
    <div class="container">
        <div class="right-panel">
            <h2>cadastrar</h2>
            <div class="social-icons">
                <button>f</button>
                <button>G+</button>
                <button>in</button>
            </div>
            <form method="POST" action="">
                <select name="cargo" required>
                    <option value="">Selecione o cargo</option>
                    <option value="usuario">Usuário</option>
                    <option value="admin">Administrador</option>
                </select>

                <input type="text" name="login" placeholder="Login" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" class="btn-cadastrar">Cadastrar-se</button>
            </form>
        </div>
        <div class="left-panel">
            <img id="logo" src="./img/logo.png" alt="Logo" class="logo">
            <h2>cadastre-se agora</h2>
            <p>já tem uma conta?<br>Clice aqui em baixo para logar</p>
            <a href="login.php"><button class="btn-voltar">Voltar</button></a>
        </div>
    </div>
</body>

</html>