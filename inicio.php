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

// Busca total de itens por categoria (ou por localizacao, se preferir)
$sql = "SELECT categoria, SUM(quantidade) AS total FROM itens GROUP BY categoria";
$resultado = $conn->query($sql);

// Arrays para o gráfico
$categorias = [];
$totais = [];
$totalGeral = 0;

while ($linha = $resultado->fetch_assoc()) {
    $categorias[] = $linha['categoria'];
    $totais[] = $linha['total'];
    $totalGeral += $linha['total'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página inicial</title>
    <link rel="stylesheet" href="./css/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <DIv id="DIVELOGO">

                <img id="logo" src="./img/logo.png" alt="Logo Móveis Armazenamento">
                
                <ul class="menu">
                    <li><a href="#"><img class="icone" src="./img/casa.png" alt=""> Início</a></li>
                    <li><a href="./estoque.php"><img class="icone" src="./img/caixa-aberta.png" alt=""> Estoque</a></li>
                    <li><a href="./novo_usuario.php"><img class="icone" src="./img/do-utilizador.png" alt=""> Novo usuário</a></li>
                    <li><a href="./hisorico.php"><img class="icone" src="./img/prancheta.png" alt=""> Histórico</a></li>
                </ul>
                
            </DIv>
            <a id="sair" href="index.php">< Sair</a>
        </nav>

        <main class="conteudo">
            <header class="topo">
                <h1>Inicio</h1>
                <div class="usuario-info">
                <strong><?= htmlspecialchars($usuario_nome) ?></strong><br>
                <small><?= htmlspecialchars($usuario_email) ?></small>
                </div>
            </header>

        <meta charset="UTF-8">
    <title>Total de Itens</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <h2>Total de Itens</h2>
    <div class="container" style="margin-top: 20px; margin-right: 110px;" >
        <div style="width:400px; height:400px; margin:auto;">
            <canvas id="grafico"></canvas>
        </div>

           <div class="cards">
          <div class="card" onclick="window.location.href='estoque.php'">
            <i class="fa fa-box"></i>
            <p>Total de Itens</p>
            <div class="valor"><?= $totalGeral ?></div>
          </div>
        
          <?php 
          $icones = [
            'cozinha' => 'fa-utensils',
            'quarto' => 'fa-bed',
            'sala de estar' => 'fa-couch',
            'banheiro' => 'fa-bath',
            'escritorio' => 'fa-briefcase',
            'garagem' => 'fa-car'
          ];
      
          foreach ($categorias as $i => $cat): 
            $icone = isset($icones[strtolower($cat)]) ? $icones[strtolower($cat)] : 'fa-box';
          ?>
            <div 
              class="card" 
              onclick="window.location.href='estoque.php?categoria=<?= urlencode($cat) ?>'"
              style="cursor:pointer;"
            >
              <i class="fa <?= $icone ?>"></i>
              <p><?= ucfirst($cat) ?></p>
              <div class="valor"><?= $totais[$i] ?></div>
            </div>
          <?php endforeach; ?>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('grafico').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($categorias) ?>,
                datasets: [{
                    data: <?= json_encode($totais) ?>,
                    backgroundColor: ['#4ADE80', '#Ff5C55', '#22D3EE', '#5A5C55', '#6366F1', '#FACC15']
                    
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>