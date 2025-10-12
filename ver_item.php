<?php
session_start();

// ===== CONEXÃO COM O BANCO =====
include('conexao.php');

// ===== VERIFICA LOGIN =====
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$usuario_nome  = $_SESSION['usuario_nome'] ?? 'Usuário';
$usuario_email = $_SESSION['usuario_email'] ?? '---';
$usuario_tipo  = $_SESSION['usuario_tipo'] ?? 'funcionario';

// ===== VERIFICA SE FOI PASSADO UM ID DE ITEM =====
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: estoque.php");
    exit;
}

$id_item = intval($_GET['id']);

// ====== PROCESSA ENVIO DE COMENTÁRIO ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'comentar') {
    $usuario_id = intval($_SESSION['usuario_id']);
    $texto = trim($_POST['comentario'] ?? '');

    if ($texto !== '') {
        $sql_ins = "INSERT INTO comentarios (item_id, usuario_id, texto) VALUES (?, ?, ?)";
        $stmt_ins = $conn->prepare($sql_ins);
        if ($stmt_ins) {
            $stmt_ins->bind_param("iis", $id_item, $usuario_id, $texto);
            $stmt_ins->execute();
            $stmt_ins->close();
        } else {
            error_log("Erro prepare insert comentario: " . $conn->error);
        }
    }

    // Evita reenvio de formulário no refresh
    header("Location: ver_item.php?id=" . $id_item);
    exit;
}
// ===== PROCESSA MOVIMENTAÇÃO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo'])) {
    $tipo = $_POST['tipo']; // entrada ou saida
    $quantidade = intval($_POST['quantidade']);
    $data = $_POST['data'] ?? date("Y-m-d H:i:s");
    $responsavel_id = intval($_SESSION['usuario_id']);
    $observacoes = null; // opcional

    // busca quantidade atual
    $stmt_q = $conn->prepare("SELECT quantidade FROM itens WHERE id = ?");
    $stmt_q->bind_param("i", $id_item);
    $stmt_q->execute();
    $res_q = $stmt_q->get_result();
    $item_atual = $res_q->fetch_assoc();
    $qtd_atual = $item_atual['quantidade'] ?? 0;

    // valida quantidade
    if ($quantidade <= 0) {
        $erro_mov = "Quantidade inválida.";
    } elseif ($tipo === 'saida' && $quantidade > $qtd_atual) {
        $erro_mov = "Não é possível remover mais do que a quantidade atual ($qtd_atual).";
    } else {
        // iniciar transação
        $conn->begin_transaction();

        try {
            if ($tipo === 'entrada') {
                // insere entrada
                $stmt = $conn->prepare("INSERT INTO entradas (item_id, quantidade, data_entrada, responsavel_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisi", $id_item, $quantidade, $data, $responsavel_id);
                $stmt->execute();

                // atualiza quantidade
                $stmt2 = $conn->prepare("UPDATE itens SET quantidade = quantidade + ? WHERE id = ?");
                $stmt2->bind_param("ii", $quantidade, $id_item);
                $stmt2->execute();
            } else {
                // insere saída
                $stmt = $conn->prepare("INSERT INTO saidas (item_id, quantidade, data_saida, responsavel_id) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisi", $id_item, $quantidade, $data, $responsavel_id);
                $stmt->execute();

                // atualiza quantidade
                $stmt2 = $conn->prepare("UPDATE itens SET quantidade = quantidade - ? WHERE id = ?");
                $stmt2->bind_param("ii", $quantidade, $id_item);
                $stmt2->execute();
            }

            // registra no histórico
            $stmt3 = $conn->prepare("INSERT INTO historico (tipo, item_id, quantidade, responsavel_id, data_movimentacao) VALUES (?, ?, ?, ?, ?)");
            $stmt3->bind_param("siiss", $tipo, $id_item, $quantidade, $responsavel_id, $data);
            $stmt3->execute();

            $conn->commit();

            // redireciona para atualizar a página e fechar o modal
            header("Location: ver_item.php?id=" . $id_item);
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $erro_mov = "Erro ao salvar movimentação: " . $e->getMessage();
        }
    }
}

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

// =========================
// BUSCA OS COMENTÁRIOS
// =========================
$sql_com = "SELECT c.id, c.texto, c.criado_em, c.usuario_id, u.nome AS usuario_nome
            FROM comentarios c
            LEFT JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.item_id = ?
            ORDER BY c.criado_em DESC";

$stmt_com = $conn->prepare($sql_com);
if ($stmt_com) {
    $stmt_com->bind_param("i", $id_item);
    $stmt_com->execute();
    $comentarios = $stmt_com->get_result();
} else {
    $comentarios = false;
}
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

    <div class="detalhes-grid">
        <!-- ===== TABELA HISTÓRICO ===== -->
        <section class="tabela-historico">
            <?php if (!empty($erro_mov)): ?>
                <div style="color:red; margin-bottom:10px; font-weight:bold;">
                    <?= htmlspecialchars($erro_mov) ?>
                </div>
            <?php endif; ?>
            <div class="titulo-historico">
                <h2>Histórico</h2>
                <img id="btnAddMov" src="./img/add.png" alt="Adicionar" class="btn-icone">
            </div>


                <!-- Modal -->
                <div id="modalMov" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <h3>Nova Movimentação</h3>
                        <form method="post" name="movimentacao">
                            <label for="tipo">Tipo:</label>
                            <select id="tipo" name="tipo" required>
                                <option value="entrada">Entrada</option>
                                <option value="saida">Saída</option>
                            </select>

                            <label for="data">Data:</label>
                            <input type="date" id="data" name="data" required>

                            <label for="quantidade">Quantidade:</label>
                            <input type="number" id="quantidade" name="quantidade" min="1" required>

                            <button type="submit">Salvar</button>
                        </form>

                    </div>
                </div>
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Qtd.</th>
                        <th>Responsável</th>
                        <?php if ($usuario_tipo === 'admin'): ?>
                            <th>Ações</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $historico->fetch_assoc()): ?>
                        <tr>
                            <td><?= date("d/m/Y H:i", strtotime($row['data_movimentacao'])) ?></td>
                            <td><?= ucfirst($row['tipo']) ?></td>
                            <td><?= $row['quantidade'] ?></td>
                            <td><?= htmlspecialchars($row['responsavel'] ?? '---') ?></td>
                            <?php if ($usuario_tipo === 'admin'): ?>
                                <td>
                                    <a href="#" 
                                    class="btn-editar-historico" 
                                    data-id="<?= $row['id'] ?>"
                                    data-tipo="<?= $row['tipo'] ?>"
                                    data-quantidade="<?= $row['quantidade'] ?>"
                                    title="Editar entrada/saída">
                                    <img src="./img/editar.png" alt="Editar">
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- ===== MODAL EDITAR MOVIMENTAÇÃO ===== -->
            <div id="modalEditarHistorico" class="modal">
                <div class="modal-content">
                    <span class="fechar editar-historico-close">&times;</span>
                    <h3>Editar Movimentação</h3>
                    <form id="formEditarHistorico" method="POST" action="editar_historico.php">
                        <input type="hidden" name="id" id="edit-historico-id">

                        <label for="edit-tipo">Tipo:</label>
                        <select id="edit-tipo" name="tipo" required>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>

                        <label for="edit-quantidade">Quantidade:</label>
                        <input type="number" id="edit-quantidade" name="quantidade" min="1" required>

                        <button type="submit">Salvar Alterações</button>
                    </form>
                </div>
            </div>
            <!-- ===== MODAL EDITAR MOVIMENTAÇÃO ===== -->
            <div class="qtd-atual">Qtd. atual: <?= htmlspecialchars($item['quantidade']) ?></div>
        </section>

        <!-- ===== OBSERVAÇÕES / COMENTÁRIOS ===== -->
        <section class="observacoes">
            <h2>Observações</h2>

            <div class="comentarios-lista" role="log" aria-live="polite">
                <?php if ($comentarios && $comentarios->num_rows > 0): ?>
                    <?php while ($c = $comentarios->fetch_assoc()): ?>
                        <div class="box-observacao">
                            <strong><?= htmlspecialchars($c['usuario_nome'] ?? 'Usuário') ?></strong>
                            <p><?= nl2br(htmlspecialchars($c['texto'])) ?></p>
                            <small><?= date("d/m/Y H:i", strtotime($c['criado_em'])) ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="box-observacao">
                        <p>Nenhum comentário.</p>
                    </div>
                <?php endif; ?>
            </div>

            <form class="comentar" method="post" action="">
                <input type="hidden" name="acao" value="comentar">
                <textarea name="comentario" placeholder="Adicionar comentário..." rows="3" required></textarea>
                <button type="submit">Comentar</button>
            </form>
        </section>

    </div>
</main>
</div>
<script>
const modal = document.getElementById('modalMov');
const btn = document.getElementById('btnAddMov');
const span = document.querySelector('.modal .close');

btn.onclick = () => modal.style.display = 'block';
span.onclick = () => modal.style.display = 'none';
window.onclick = (e) => { if (e.target == modal) modal.style.display = 'none'; }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const modalEditarHist = document.getElementById("modalEditarHistorico");
    const fecharEditarHist = document.querySelector(".editar-historico-close");

    document.querySelectorAll(".btn-editar-historico").forEach(btn => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();

            // Preenche os campos do modal
            document.getElementById("edit-historico-id").value = btn.dataset.id;
            document.getElementById("edit-tipo").value = btn.dataset.tipo;
            document.getElementById("edit-quantidade").value = btn.dataset.quantidade;

            modalEditarHist.style.display = "flex";
        });
    });

    // Fechar modal ao clicar no X
    fecharEditarHist.addEventListener("click", () => {
        modalEditarHist.style.display = "none";
    });

    // Fechar modal ao clicar fora
    window.addEventListener("click", (e) => {
        if (e.target === modalEditarHist) modalEditarHist.style.display = "none";
    });
});
</script>

</body>
</html>
