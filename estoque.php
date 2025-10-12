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

if (!$resultado) {
    die("Erro ao buscar itens: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque - Projeto Express</title>
    <link rel="stylesheet" href="./css/estoque.css">
    <link rel="stylesheet" href="./css/inicio.css">
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <nav class="sidebar">
            <DIv id="DIVELOGO">

                <img id="logo" src="./img/logo.png" alt="Logo Móveis Armazenamento">
                
                <ul class="menu">
                    <li><a href="./inicio.php"><img class="icone" src="./img/casa.png" alt=""> Início</a></li>
                    <li><a href="./estoque.php"><img class="icone" src="./img/caixa-aberta.png" alt=""> Estoque</a></li>
                    <li><a href="./ver_item.php"><img class="icone" src="./img/do-utilizador.png" alt=""> Novo usuário</a></li>
                    <li><a href="./hisorico.php"><img class="icone" src="./img/prancheta.png" alt=""> Histórico</a></li>
                </ul>
                
            </DIv>
            <a id="sair" href="index.php">< Sair</a>
        </nav>
    <!-- Conteúdo -->
    <main class="conteudo">
        <header class="topo">
            <h1>Estoque</h1>
            <div class="usuario-info">
                <strong><?= htmlspecialchars($usuario_nome) ?></strong><br>
                <small><?= htmlspecialchars($usuario_email) ?></small>
            </div>
        </header>

        <section class="painel-estoque">
           <div class="botoes-superior">
                <!-- Campo de pesquisa com ícone de background -->
                <input type="text" placeholder="Pesquisar item..." id="campo-pesquisa" class="input-pesquisa">
                <!-- Botão Filtrar com dropdown -->
                <div class="dropdown-filtrar">
                    <button class="btn-filtrar" id="btn-filtrar">
                        Filtrar
                        <img src="./img/Filtro.png" alt="Filtrar" class="icone-btn">
                    </button>
                    <div class="dropdown-content" id="dropdown-categorias">
                        <!-- Categorias serão inseridas pelo JS -->
                    </div>
                </div>
                <button class="btn-novo">+ Novo Item</button>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-estoque">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Categoria</th>
                            <th>Nome</th>
                            <th>Qtd.</th>
                            <th>Local</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($resultado->num_rows > 0): ?>
                            <?php while ($item = $resultado->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($item['codigo']) ?></td>
                                    <td><?= htmlspecialchars($item['categoria']) ?></td>
                                    <td><?= htmlspecialchars($item['nome']) ?></td>
                                    <td><?= htmlspecialchars($item['quantidade']) ?></td>
                                    <td><?= htmlspecialchars($item['localizacao']) ?></td>
                                    <td class="acoes">
                                        <a href="ver_item.php?id=<?= $item['id'] ?>" title="Ver"><img src="./img/olho.png" alt="Ver"></a>
                                        <?php if ($usuario_tipo === 'admin'): ?>
                                            <a href="#" 
                                                class="btn-editar" 
                                                data-id="<?= $item['id'] ?>" 
                                                data-nome="<?= htmlspecialchars($item['nome']) ?>" 
                                                data-codigo="<?= htmlspecialchars($item['codigo']) ?>" 
                                                data-categoria="<?= htmlspecialchars($item['categoria']) ?>" 
                                                data-quantidade="<?= htmlspecialchars($item['quantidade']) ?>" 
                                                data-localizacao="<?= htmlspecialchars($item['localizacao']) ?>" 
                                                title="Editar">
                                                <img src="./img/editar.png" alt="Editar">
                                            </a>
                                            <a href="#" 
                                                class="btn-excluir" 
                                                data-id="<?= $item['id'] ?>" 
                                                data-nome="<?= htmlspecialchars($item['nome']) ?>" 
                                                title="Excluir">
                                                <img src="./img/lixo.png" alt="Excluir">
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">Nenhum item encontrado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <!-- ===== MODAL NOVO ITEM ===== -->
        <div id="modalNovoItem" class="modal">
        <div class="modal-content">
            <span class="fechar">&times;</span>
            <h2>🆕 Novo Item</h2>
            <form id="formNovoItem" method="POST" action="salvar_item.php">
            <div class="grupo-input">
                <label>Nome do Item</label>
                <input type="text" name="nome" required>
            </div>

            <div class="grupo-input">
                <label>Código</label>
                <input type="text" name="codigo" required>
            </div>

            <div class="grupo-input">
              <label>Categoria</label>
              <select name="categoria" required>
                <option value="">Selecione...</option>
                <option value="cozinha">Cozinha</option>
                <option value="quarto">Quarto</option>
                <option value="sala de estar">Sala de estar</option>
                <option value="banheiro">Banheiro</option>
                <option value="escritorio">Escritório</option>
                <option value="garagem">Garagem</option>
              </select>
            </div>

            <div class="grupo-input">
                <label>Quantidade</label>
                <input type="number" name="quantidade" min="0" value="0">
            </div>

            <div class="grupo-input">
                <label>Localização</label>
                <select name="localizacao" required>
                <option value="Setor A">Setor A</option>
                <option value="Setor B">Setor B</option>
                <option value="Setor C">Setor C</option>
                </select>
            </div>

            <div class="grupo-input">
                <label>Observações</label>
                <textarea name="observacoes" rows="3"></textarea>
            </div>

            <button type="submit" class="btn-salvar">Salvar Item</button>
            </form>
        </div>
        </div>
        <!-- ===== FIM MODAL NOVO ITEM ===== -->
        <!-- ===== MODAL EDITAR ITEM ===== --> 
        <div id="modalEditarItem" class="modal">
            <div class="modal-content">
                <span class="fechar fechar-editar">&times;</span>
                <h2>✏️ Editar Item</h2>
                <form id="formEditarItem" method="POST" action="editar_item_salvar.php">
                    <input type="hidden" name="id" id="edit-id">

                    <div class="grupo-input">
                        <label>Nome do Item</label>
                        <input type="text" name="nome" id="edit-nome" required>
                    </div>

                    <div class="grupo-input">
                        <label>Categoria</label>
                        <select name="categoria" id="edit-categoria" required>
                            <option value="">Selecione...</option>
                            <option value="cozinha">Cozinha</option>
                            <option value="quarto">Quarto</option>
                            <option value="sala de estar">Sala de estar</option>
                            <option value="banheiro">Banheiro</option>
                            <option value="escritorio">Escritório</option>
                            <option value="garagem">Garagem</option>
                        </select>
                    </div>

                    <div class="grupo-input">
                        <label>Localização</label>
                        <select name="localizacao" id="edit-localizacao" required>
                            <option value="Setor A">Setor A</option>
                            <option value="Setor B">Setor B</option>
                            <option value="Setor C">Setor C</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-salvar">Salvar Alterações</button>
                </form>
            </div>
        </div>
        <!-- ===== FIM MODAL EDITAR ITEM ===== -->

             <script>
document.addEventListener("DOMContentLoaded", function() {
    const botoesExcluir = document.querySelectorAll(".btn-excluir");

    botoesExcluir.forEach(botao => {
        botao.addEventListener("click", function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const nome = this.dataset.nome;
            const linha = this.closest("tr");

            Swal.fire({
                title: "Tem certeza?",
                text: `Deseja excluir o item "${nome}"?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sim, excluir",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("excluir_item.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "id=" + encodeURIComponent(id)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Item excluído!",
                                text: `"${nome}" foi removido com sucesso.`,
                                showConfirmButton: false,
                                timer: 1800
                            });
                            linha.remove(); // remove a linha da tabela
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Erro ao excluir",
                                text: data.error || "Não foi possível excluir o item."
                            });
                        }
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: "error",
                            title: "Erro de conexão",
                            text: "Não foi possível comunicar com o servidor."
                        });
                    });
                }
            });
        });
    });
});
</script>


        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("modalNovoItem");
            const btnNovo = document.querySelector(".btn-novo");
            const spanFechar = document.querySelector(".fechar");

            if (btnNovo && modal && spanFechar) {
                // Abrir modal
                btnNovo.addEventListener("click", () => {
                    modal.style.display = "flex";
                });

                // Fechar modal ao clicar no X
                spanFechar.addEventListener("click", () => {
                    modal.style.display = "none";
                });

                // Fechar modal ao clicar fora
                window.addEventListener("click", (e) => {
                    if (e.target === modal) modal.style.display = "none";
                });
            }
        });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
            const modalEditar = document.getElementById("modalEditarItem");
            const fecharEditar = document.querySelector(".fechar-editar");

            document.querySelectorAll(".btn-editar").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    e.preventDefault();

                    // Preenche apenas os campos que existem no modal
                    document.getElementById("edit-id").value = btn.dataset.id;
                    document.getElementById("edit-nome").value = btn.dataset.nome;
                    document.getElementById("edit-categoria").value = btn.dataset.categoria;
                    document.getElementById("edit-localizacao").value = btn.dataset.localizacao;

                    // Mostra o modal
                    modalEditar.style.display = "flex";
                });
            });

            fecharEditar.addEventListener("click", () => {
                modalEditar.style.display = "none";
            });

            window.addEventListener("click", (e) => {
                if (e.target === modalEditar) modalEditar.style.display = "none";
            });
        });
            </script>
            <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const tabela = document.querySelector(".tabela-estoque tbody");
                        const linhas = Array.from(tabela.querySelectorAll("tr")).filter(l => l.cells.length > 1);

                        const inputPesquisa = document.getElementById("campo-pesquisa"); // usa o ID do seu input!
                        const btnFiltrar = document.getElementById("btn-filtrar");

                        function filtrarTabela() {
                            const filtro = inputPesquisa.value.toLowerCase();
                            linhas.forEach(linha => {
                                if (linha.cells.length === 1) return;
                                const codigo = linha.cells[0].textContent.toLowerCase();
                                const categoria = linha.cells[1].textContent.toLowerCase();
                                const nome = linha.cells[2].textContent.toLowerCase();

                                if (codigo.includes(filtro) || categoria.includes(filtro) || nome.includes(filtro)) {
                                    linha.style.display = "";
                                } else {
                                    linha.style.display = "none";
                                }
                            });
                        }

                    
                        inputPesquisa.addEventListener("keyup", filtrarTabela);
                        btnFiltrar.addEventListener("click", filtrarTabela);

                    
                        const params = new URLSearchParams(window.location.search);
                        const categoriaURL = params.get("categoria");

                        if (categoriaURL) {
                            inputPesquisa.value = categoriaURL;
                            filtrarTabela();
                        }
                    });
            </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </main>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const inputPesquisa = document.getElementById("campo-pesquisa");
    const btnFiltrar = document.querySelector(".btn-filtrar");
    const tabela = document.querySelector(".tabela-estoque tbody");
    const linhas = tabela.querySelectorAll("tr");

    function filtrarTabela() {
        const filtro = inputPesquisa.value.toLowerCase();

        linhas.forEach(linha => {
            // Ignora linhas de "Nenhum item encontrado"
            if (linha.cells.length === 1) return;

            const codigo = linha.cells[0].textContent.toLowerCase();
            const categoria = linha.cells[1].textContent.toLowerCase();
            const nome = linha.cells[2].textContent.toLowerCase();

            if (codigo.includes(filtro) || categoria.includes(filtro) || nome.includes(filtro)) {
                linha.style.display = "";
            } else {
                linha.style.display = "none";
            }
        });
    }

    // Filtrar ao digitar
    inputPesquisa.addEventListener("keyup", filtrarTabela);

    // Filtrar ao clicar no botão
    btnFiltrar.addEventListener("click", filtrarTabela);
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const tabela = document.querySelector(".tabela-estoque tbody");
    const linhas = Array.from(tabela.querySelectorAll("tr")).filter(l => l.cells.length > 1); // ignora mensagem "nenhum item"
    
    const inputPesquisa = document.getElementById("input-pesquisa");
    const btnFiltrar = document.getElementById("btn-filtrar");
    const dropdown = document.getElementById("dropdown-categorias");

    // Criar lista de categorias com soma de quantidades
    const categoriasMap = {};
    linhas.forEach(linha => {
        const categoria = linha.cells[1].textContent;
        const quantidade = parseInt(linha.cells[3].textContent);
        if (!categoriasMap[categoria]) categoriasMap[categoria] = 0;
        categoriasMap[categoria] += quantidade;
    });

    // Ordenar categorias por quantidade descendente
    const categoriasOrdenadas = Object.entries(categoriasMap).sort((a,b) => b[1] - a[1]);

    // Popular dropdown
    categoriasOrdenadas.forEach(([categoria, total]) => {
        const div = document.createElement("div");
        div.textContent = `${categoria} (${total})`;
        div.dataset.categoria = categoria;
        dropdown.appendChild(div);
    });

    // Mostrar/ocultar dropdown ao clicar no botão
    btnFiltrar.addEventListener("click", () => {
        dropdown.classList.toggle("show");
    });

    // Filtrar tabela ao selecionar categoria
    dropdown.addEventListener("click", (e) => {
        const categoriaSelecionada = e.target.dataset.categoria;
        if (!categoriaSelecionada) return;

        // Ocultar todas as linhas
        linhas.forEach(linha => linha.style.display = "none");

        // Exibir apenas itens da categoria selecionada, ordenados por quantidade descendente
        const itensFiltrados = linhas
            .filter(l => l.cells[1].textContent === categoriaSelecionada)
            .sort((a,b) => parseInt(b.cells[3].textContent) - parseInt(a.cells[3].textContent));

        itensFiltrados.forEach(linha => linha.style.display = "");

        dropdown.classList.remove("show");
    });

    // Fechar dropdown ao clicar fora
    window.addEventListener("click", (e) => {
        if (!btnFiltrar.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove("show");
        }
    });
});
</script>


</body>
</html>

