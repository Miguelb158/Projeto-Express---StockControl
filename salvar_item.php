<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe dados do formulário
    $codigo       = $_POST['codigo'];
    $nome         = $_POST['nome'];
    $categoria    = $_POST['categoria'] ?? '';
    $quantidade   = intval($_POST['quantidade']);
    $localizacao  = $_POST['localizacao'];
    $observacoes  = $_POST['observacoes'] ?? '';
    $usuario_id   = $_SESSION['usuario_id']; // responsável
    $usuario_nome = $_SESSION['usuario_nome'];

    // 1️⃣ Inserir item na tabela itens
    $sql_item = "INSERT INTO itens (codigo, nome, categoria, quantidade, localizacao, observacoes, criado_por)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    $stmt_item->bind_param("sssissi", $codigo, $nome, $categoria, $quantidade, $localizacao, $observacoes, $usuario_id);

    if ($stmt_item->execute()) {
        $item_id = $stmt_item->insert_id; // ID do item recém-criado

        // 2️⃣ Registrar entrada se quantidade > 0
        if ($quantidade > 0) {
            $sql_entrada = "INSERT INTO entradas (item_id, quantidade, responsavel_id, observacoes)
                            VALUES (?, ?, ?, ?)";
            $stmt_entrada = $conn->prepare($sql_entrada);
            $stmt_entrada->bind_param("iiis", $item_id, $quantidade, $usuario_id, $observacoes);
            $stmt_entrada->execute();

            // 3️⃣ Registrar no histórico
            $sql_historico = "INSERT INTO historico (tipo, item_id, quantidade, responsavel_id, observacoes)
                              VALUES ('entrada', ?, ?, ?, ?)";
            $stmt_hist = $conn->prepare($sql_historico);
            $stmt_hist->bind_param("iiis", $item_id, $quantidade, $usuario_id, $observacoes);
            $stmt_hist->execute();
        }

        // Sucesso
        echo "<script>alert('✅ Item adicionado com sucesso por $usuario_nome!'); window.location.href='estoque.php';</script>";
        exit;

    } else {
        // Erro ao inserir
        echo "<script>alert('❌ Erro ao salvar item: {$conn->error}'); history.back();</script>";
        exit;
    }
}
?>
