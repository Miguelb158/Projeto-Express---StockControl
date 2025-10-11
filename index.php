<?php
include('conexao.php');
session_start();

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']); // agora corresponde ao input
    $senha = trim($_POST['senha']);

    if (empty($email) || empty($senha)) {
        $mensagem = "⚠️ Preencha todos os campos!";
    } else {
        // Prepara a consulta de forma segura
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
        if (!$stmt) {
            die("Erro ao preparar consulta: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();

            if (password_verify($senha, $usuario['senha'])) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_tipo'] = $usuario['tipo'];

                header("Location: inicio.php");
                exit;
            } else {
                $mensagem = "❌ Senha incorreta!";
            }
        } else {
            $mensagem = "⚠️ Usuário não encontrado!";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ProjetoExpress</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container">
        <div class="left-panel">
            <img id="logo" src="./img/logo.png" alt="Logo" class="logo">
            <h2>Seja bem-vindo</h2>
            <p>
                Por enquanto, somos um sistema de <strong>gerenciamento de estoque de móveis</strong>.<br>
                Aqui você controla entradas, saídas e observações de cada item — garantindo mais
                organização e decisões assertivas.
            </p>
            <a href="cadastrar.php"><button class="btn-voltar">Cadastrar</button></a>
        </div>

        <div class="right-panel">
            <h2>Login</h2>

            <?php if (!empty($mensagem)): ?>
                <div class="alert erro"><?= htmlspecialchars($mensagem) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit" class="btn-cadastrar">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>

