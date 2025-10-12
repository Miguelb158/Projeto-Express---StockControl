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

$sql = "SELECT id, codigo, categoria, nome, quantidade, localizacao FROM itens ORDER BY id ASC";
$resultado = $conn->query($sql);
$sql = "SELECT 
            h.id,
            h.tipo,
            i.nome AS item,
            h.quantidade,
            r.nome AS responsavel,
            DATE_FORMAT(h.data_movimentacao, '%d/%m/%Y %H:%i:%s') AS data
        FROM historico h
        JOIN itens i ON h.item_id = i.id
        JOIN usuarios r ON h.responsavel_id = r.id
        ORDER BY h.data_movimentacao DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página inicial</title>
    <link rel="stylesheet" href="./css/inicio.css">
    <link rel="stylesheet" href="./css/historico.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <DIv id="DIVELOGO">

                <img id="logo" src="./img/logo.png" alt="Logo Móveis Armazenamento">
                
                <ul class="menu">
                    <li><a href="./inicio.php"><img class="icone" src="./img/casa.png" alt=""> Início</a></li>
                    <li><a href="./estoque.php"><img class="icone" src="./img/caixa-aberta.png" alt=""> Estoque</a></li>
                    <li><a href="#"><img class="icone" src="./img/do-utilizador.png" alt=""> Novo usuário</a></li>
                    <li><a href="#"><img class="icone" src="./img/prancheta.png" alt=""> Histórico</a></li>
                </ul>
                
            </DIv>
            <a id="sair" href="index.php">< Sair</a>
        </nav>
        <div class="container">
        <h2>Histórico</h2>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Item</th>
                    <th>Qtd.</th>
                    <th>Responsável</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['data'] ?></td>
                        <td><?= ucfirst($row['tipo']) ?></td>
                        <td><?= $row['item'] ?></td>
                        <td><?= $row['quantidade'] ?></td>
                        <td><?= $row['responsavel'] ?></td>
                        <td class="acoes">
                            <a href="editar.php?id=<?= $row['id'] ?>" class="edit">✏️</a>
                            <a href="ver.php?id=<?= $row['id'] ?>" class="view">👁️</a>
                            <a href="remover.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Tem certeza que deseja excluir?')">🗑️</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
