<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $tipo = $_POST['tipo'];
    $quantidade = intval($_POST['quantidade']);

    // Busca dados do histórico antigo
    $sql = "SELECT item_id, tipo, quantidade FROM historico WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $hist = $res->fetch_assoc();
    $stmt->close();

    if ($hist) {
        $item_id = $hist['item_id'];
        $qtd_antiga = $hist['quantidade'];
        $tipo_antigo = $hist['tipo'];

        // Atualiza histórico
        $sql = "UPDATE historico SET tipo = ?, quantidade = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $tipo, $quantidade, $id);
        $stmt->execute();
        $stmt->close();

        // Recalcula quantidade final do item
        $sql = "SELECT SUM(CASE WHEN tipo='entrada' THEN quantidade ELSE -quantidade END) as total
                FROM historico WHERE item_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $total = $res->fetch_assoc()['total'] ?? 0;
        $stmt->close();

        // Atualiza quantidade do item
        $sql = "UPDATE itens SET quantidade=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $total, $item_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Movimentação atualizada com sucesso!'); window.location.href='ver_item.php?id=$item_id';</script>";
    } else {
        echo "<script>alert('Erro: histórico não encontrado.'); history.back();</script>";
    }

    $conn->close();
}
?>
