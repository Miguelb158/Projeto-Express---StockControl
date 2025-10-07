<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM usuarios WHERE login = '$login'";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            // Login correto
            session_start();
            $_SESSION['usuario'] = $usuario['login'];
            $_SESSION['cargo'] = $usuario['cargo'];

            // Redireciona conforme o cargo
            header("Location: inicio.php");
            exit;
        } else {
            // Senha incorreta
            header("Location: index.php?erro=Senha+incorreta!");
            exit;
        }
    } else {
        // Usuário não encontrado
        header("Location: index.php?erro=Usuário+não+encontrado!");
        exit;
    }
}
?>