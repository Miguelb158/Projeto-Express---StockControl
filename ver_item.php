<?php
session_start();
include('conexao.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$usuario_nome  = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_email = $_SESSION['usuario_email'] ?? '---';
$usuario_tipo  = $_SESSION['usuario_tipo'] ?? 'funcionario';

// Verifica se foi passado um ID na URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: estoque.php");
    exit;
}

$id_item = intval($_GET['id']);

// =========================
// BUSCA OS DADOS DO ITEM
// =========================
$sql_item = "SELECT i.id, i.codigo, i.nome, i.categoria, i.quantidade, i.localizacao, i.observacoes, 
                    u.nome AS criado_por
             FROM itens i
             LEFT JOIN usuarios u ON i.criado_por = u.id
             WHERE i.id = ?";

$stmt = $conn->prepare($sql_item);

if (!$stmt) {
    die("Erro na preparação da query: " . $conn->error);
}

$stmt->bind_param("i", $id_item);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    die("Item não encontrado.");
}

// =========================
// BUSCA O HISTÓRICO DO ITEM
// =========================
$sql_hist = "SELECT h.id, h.tipo, h.quantidade, h.data_movimentacao, 
                    u.nome AS responsavel
             FROM historico h
             LEFT JOIN usuarios u ON h.responsavel_id = u.id
             WHERE h.item_id = ?
             ORDER BY h.data_movimentacao DESC";

$stmt_hist = $conn->prepare($sql_hist);
$stmt_hist->bind_param("i", $id_item);
$stmt_hist->execute();
$historico = $stmt_hist->get_result();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Item - <?= htmlspecialchars($item['nome']) ?></title>
    <link rel="stylesheet" href="./css/ver_item.css">
    <link rel="stylesheet" href="./css/inicio.css">
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <nav class="sidebar">
        <div id="DIVELOGO">
            <img id="logo" src="./img/logo.png" alt="Logo Móveis Armazenamento">
            <ul class="menu">
                <li><a href="./inicio.php"><img class="icone" src="./img/casa.png" alt=""> Início</a></li>
                <li><a href="./estoque.php"><img class="icone" src="./img/caixa-aberta.png" alt=""> Estoque</a></li>
                <li><a href="#"><img class="icone" src="./img/do-utilizador.png" alt=""> Novo usuário</a></li>
                <li><a href="#"><img class="icone" src="./img/prancheta.png" alt=""> Histórico</a></li>
            </ul>
        </div>
        <a id="sair" href="index.php">< Sair</a>
    </nav>

    <!-- Conteúdo -->
    <main class="conteudo">
        <header class="topo">
            <h1>Detalhes do Item <span style="color: gray;"> / <?= htmlspecialchars($item['nome']) ?></span></h1>
            <div class="usuario-info">
                <strong><?= htmlspecialchars($usuario_nome) ?></strong><br>
                <small><?= htmlspecialchars($usuario_email) ?></small>
            </div>
        </header>

        <section class="tabela-historico">
            <h2>Histórico</h2>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Qtd.</th>
                        <th>Responsável</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $historico->fetch_assoc()): ?>
                        <tr>
                            <td><?= date("d/m/Y H:i", strtotime($row['data_movimentacao'])) ?></td>
                            <td><?= ucfirst($row['tipo']) ?></td>
                            <td><?= $row['quantidade'] ?></td>
                            <td><?= htmlspecialchars($row['responsavel'] ?? '---') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>
</body>
</html>
