<?php

include 'includes/header.php'; 
include 'includes/verifica_login.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançamentos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/chart.js"></script>
</head>
<body>
<div class="dashboard">
    <h2>Visão Geral Financeira</h2>

    <!-- Indicadores Principais -->
    <div class="indicadores">
        <div class="card entrada">
            <h3>Entradas</h3>
            <p>R$ <span id="total-entrada">0,00</span></p>
        </div>
        <div class="card saida">
            <h3>Saídas</h3>
            <p>R$ <span id="total-saida">0,00</span></p>
        </div>
        <div class="card saldo">
            <h3>Saldo Atual</h3>
            <p>R$ <span id="saldo-atual">0,00</span></p>
        </div>
        <div class="card vencimentos">
            <h3>A Vencer</h3>
            <p>R$ <span id="total-vencer">0,00</span></p>
        </div>
    </div>

    <!-- Gráficos -->
<div class="graficos-container">
    <div class="grafico-box">
        <canvas id="graficoBarras"></canvas>
    </div>
    <div class="grafico-box">
        <canvas id="graficoPizza"></canvas>
    </div>
</div>

    <!-- Próximos Vencimentos -->
    <h3>Próximos Vencimentos</h3>
    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody id="lista-vencimentos">
            <!-- Aqui serão inseridos os vencimentos via PHP ou JavaScript -->
        </tbody>
    </table>

    <!-- Atalhos -->
    <div class="atalhos">
        <button onclick="window.location.href='pages/lancamentos.php'">Novo Lançamento</button>
    </div>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>

