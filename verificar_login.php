<?php
session_start();
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $senha = $_POST['senha'];

    $sql = $conn ->prepare("SELECT * FROM usuarios WHERE login = ?");
    $sql->bind_param("s", $login);
    $sql->execute();
    $resultado = $sql->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            
            $_SESSION['login'] = $usuario['login'];
            $_SESSION['cargo'] = $usuario['cargo'];

            
            if ($usuario['cargo'] == 'admin') {
                header("Location: inicio.php");
            } else {
                header("Location: inicio.php");
            }
            exit;
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário não encontrado!";
    }
}
?>