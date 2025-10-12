<?php 
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);  // garante que seja número
    $nome = trim($_POST['nome']);
    $categoria = $_POST['categoria'];
    $localizacao = $_POST['localizacao'];

    $sql_atual = "SELECT codigo, quantidade FROM itens WHERE id = ?";
    $stmt_atual = $conn->prepare($sql_atual);
    $stmt_atual->bind_param("i", $id);
    $stmt_atual->execute();
    $resultado = $stmt_atual->get_result()->fetch_assoc();
    $codigo = $resultado['codigo'];
    $quantidade = $resultado['quantidade'];
    $stmt_atual->close();
    
    $sql = "UPDATE itens 
            SET nome = ?, categoria = ?, localizacao = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nome, $categoria, $localizacao, $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Item atualizado com sucesso!'); window.location.href='estoque.php';</script>";
    } else {
        echo "<script>alert('❌ Erro ao atualizar item: {$conn->error}'); history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
