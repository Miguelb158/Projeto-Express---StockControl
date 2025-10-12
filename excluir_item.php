<?php 
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);

    if ($id > 0) {

        // Começa uma transação para garantir consistência
        $conn->begin_transaction();

        try {

            $sql = "DELETE FROM historico WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM entradas WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM saidas WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $sql = "DELETE FROM itens WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            echo json_encode(['success' => true]);

        } catch (Exception $e) {

            $conn->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }

        $conn->close();

    } else {
        echo json_encode(['success' => false, 'error' => 'ID inválido']);
    }
}
?>
