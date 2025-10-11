<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('conexao.php'); 

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $tipo = $_POST['tipo']; // admin ou funcionario

    if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
        $mensagem = "⚠️ Preencha todos os campos!";
    } else {
        // Verifica se o email já existe
        $verifica = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        if (!$verifica) {
            die("Erro ao preparar verificação: " . $conn->error);
        }

        $verifica->bind_param("s", $email);
        $verifica->execute();
        $resultado = $verifica->get_result();

        if ($resultado && $resultado->num_rows > 0) {
            $mensagem = "❌ E-mail já cadastrado!";
        } else {
            // Cadastra o novo usuário
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = $conn->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (?, ?, ?, ?)");
            
            if (!$sql) {
                die("Erro ao preparar inserção: " . $conn->error);
            }

            $sql->bind_param("ssss", $nome, $email, $senha_hash, $tipo);

            if ($sql->execute()) {
                $mensagem = "✅ Cadastro realizado com sucesso!";
            } else {
                $mensagem = "Erro ao cadastrar: " . $sql->error;
            }
        }

        $verifica->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar - Projeto Express</title>
  <link rel="stylesheet" href="./css/login.css">
</head>
<body>
  <div class="container">
    <div class="right-panel">
      <h2>Cadastrar</h2>

      <?php if (!empty($mensagem)): ?>
        <p style="color: red; text-align:center;"><?php echo $mensagem; ?></p>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="text" name="nome" placeholder="Nome completo" required>
        <input type="email" name="email" placeholder="E-mail" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <select name="tipo" required>
          <option value="">Selecione o tipo de usuário</option>
          <option value="funcionario">Funcionário</option>
          <option value="admin">Administrador</option>
        </select>
        <button type="submit" class="btn-cadastrar">Cadastrar-se</button>
      </form>
    </div>

    <div class="left-panel">
      <img id="logo" src="./img/logo.png" alt="Logo" class="logo">
      <h2>Cadastre-se agora</h2>
      <p>Já tem uma conta?<br>Clique abaixo para logar</p>
      <a href="index.php"><button class="btn-voltar">Voltar</button></a>
    </div>
  </div>
</body>
</html>
