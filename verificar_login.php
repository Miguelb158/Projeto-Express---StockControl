<?php
session_start();
include("conexao.php");

$login = $_POST['login'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE login = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['id'] = $usuario['id'];
        $_SESSION['login'] = $usuario['login'];
        $_SESSION['cargo'] = $usuario['cargo'];

        if ($usuario['cargo'] === 'admin') {
            header("Location: inicio.php");
        } elseif ($usuario['cargo'] === 'funcionario') {
            header("Location: inicio.php");
        } else {
            header("Location: login.php?erro=Cargo desconhecido.");
        }
        exit;
    } else {
        header("Location: login.php?erro=Senha incorreta!");
        exit;
    }
} else {
    header("Location: login.php?erro=Usuário não encontrado!");
    exit;
}
?>
