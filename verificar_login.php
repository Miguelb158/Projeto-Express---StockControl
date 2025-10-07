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
            header("Location: admin.php");
        } else if ($usuario['cargo'] === 'funcionario') {
            header("Location: funcionario.php");
        } else {
            echo "<script>alert('Cargo desconhecido!'); window.location.href='login.php';</script>";
        }
        exit;
    } else {
        echo "<script>alert('Senha incorreta!'); window.location.href='login.php';</script>";
    }
} else {
    echo "<script>alert('Usuário não encontrado!'); window.location.href='login.php';</script>";
}
?>
