<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo       = $_POST['codigo'];
    $nome         = $_POST['nome'];
    $categoria    = $_POST['categoria'];
    $quantidade   = $_POST['quantidade'];
    $localizacao  = $_POST['localizacao'];
    $observacoes  = $_POST['observacoes'];
    $criado_por   = $_SESSION['usuario_id'];

    $sql = "INSERT INTO itens (codigo, nome, categoria, quantidade, localizacao, observacoes, criado_por)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssissi", $codigo, $nome, $categoria, $quantidade, $localizacao, $observacoes, $criado_por);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Item adicionado com sucesso!'); window.location.href='estoque.php';</script>";
    } else {
        echo "<script>alert('❌ Erro ao salvar item: {$conn->error}'); history.back();</script>";
    }
}
?>
