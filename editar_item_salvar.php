<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $codigo = $_POST['codigo'];
    $categoria = $_POST['categoria'];
    $quantidade = $_POST['quantidade'];
    $localizacao = $_POST['localizacao'];

    $sql = "UPDATE itens 
            SET nome = ?, codigo = ?, categoria = ?, quantidade = ?, localizacao = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $nome, $codigo, $categoria, $quantidade, $localizacao, $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Item atualizado com sucesso!'); window.location.href='estoque.php';</script>";
    } else {
        echo "<script>alert('❌ Erro ao atualizar item: {$conn->error}'); history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
