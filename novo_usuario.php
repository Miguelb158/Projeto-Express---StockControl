<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$usuario_nome  = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_email = $_SESSION['usuario_email'] ?? '---';
$usuario_tipo  = $_SESSION['usuario_tipo'] ?? 'funcionario';

// Processa envio do formulário
$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $tipo = $_POST['tipo'] ?? 'funcionario';

    // Só admin pode criar outro admin
    if ($usuario_tipo !== 'admin') {
        $tipo = 'funcionario';
    }

    // Valida campos
    if ($nome === '' || $email === '' || $senha === '') {
        $erro = "Por favor, preencha todos os campos obrigatórios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } else {
        // Verifica se email já existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            $stmt->close();

            // Insere usuário com senha hash
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $email, $senhaHash, $tipo);

            if ($stmt->execute()) {
                $sucesso = "Usuário criado com sucesso!";
                // Limpa campos
                $nome = $email = $senha = '';
            } else {
                $erro = "Erro ao criar usuário: " . $conn->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Usuário</title>
    <link rel="stylesheet" href="./css/inicio.css">
    <link rel="stylesheet" href="./css/novo_usuario.css">
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div id="DIVELOGO">
            <img id="logo" src="./img/logo.png" alt="Logo Móveis Armazenamento">
            <ul class="menu">
                <li><a href="./inicio.php"><img class="icone" src="./img/casa.png" alt=""> Início</a></li>
                <li><a href="./estoque.php"><img class="icone" src="./img/caixa-aberta.png" alt=""> Estoque</a></li>
                <li><a href="./novo_usuario.php"><img class="icone" src="./img/do-utilizador.png" alt=""> Novo usuário</a></li>
                <li><a href="./historico.php"><img class="icone" src="./img/prancheta.png" alt=""> Histórico</a></li>
            </ul>
        </div>
        <a id="sair" href="index.php">< Sair</a>
    </nav>

    <!-- Conteúdo -->
    <main class="conteudo">
        <header class="topo">
            <h1>Criar Novo Usuário</h1>
            <div class="usuario-info">
                <strong><?= htmlspecialchars($usuario_nome) ?></strong><br>
                <small><?= htmlspecialchars($usuario_email) ?></small>
            </div>
        </header>

        <section class="novo-usuario">
            <?php if ($erro): ?>
                <div class="erro"><?= htmlspecialchars($erro) ?></div>
            <?php endif; ?>
            <?php if ($sucesso): ?>
                <div class="sucesso"><?= htmlspecialchars($sucesso) ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="grupo-input">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($nome ?? '') ?>" required>
                </div>

                <div class="grupo-input">
                    <label>E-mail</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                </div>

                <div class="grupo-input">
                    <label>Senha</label>
                    <input type="password" name="senha" required>
                </div>

                <?php if ($usuario_tipo === 'admin'): ?>
                    <div class="grupo-input">
                        <label>Tipo de usuário</label>
                        <select name="tipo">
                            <option value="funcionario">Funcionário</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-salvar">Criar Usuário</button>
            </form>
        </section>
    </main>
</div>
</body>
</html>
