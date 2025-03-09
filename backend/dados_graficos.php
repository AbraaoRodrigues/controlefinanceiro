<?php
header("Content-Type: application/json");
include_once "../includes/conexao.php";

// Seta localidade para datas em português (opcional, para formatar nomes de meses):
$pdo->query("SET lc_time_names = 'pt_BR'");

// 1) Consulta para indicadores
$sql_indicadores = "
    SELECT 
      (SELECT IFNULL(SUM(t.valor),0) FROM transacoes t WHERE t.tipo = 'Receita') AS total_entradas,
      (SELECT IFNULL(SUM(t.valor),0) FROM transacoes t WHERE t.tipo = 'Despesa') AS total_saidas,
      (SELECT IFNULL(SUM(t.valor),0) FROM transacoes t WHERE t.status = 'Pendente') AS total_vencer
";
$stmtInd = $pdo->query($sql_indicadores);
$indic = $stmtInd->fetch(PDO::FETCH_ASSOC);
$saldo_atual = $indic['total_entradas'] - $indic['total_saidas'];

// 2) Consulta para gráfico de barras
$sql_barras = "
    SELECT 
        DATE_FORMAT(data, '%M') AS mes,
        SUM(CASE WHEN tipo = 'Receita' THEN valor ELSE 0 END) AS total_receita,
        SUM(CASE WHEN tipo = 'Despesa' THEN valor ELSE 0 END) AS total_despesa
    FROM transacoes
    GROUP BY DATE_FORMAT(data, '%Y-%m')
    ORDER BY MIN(data)
";
$stmtBarras = $pdo->query($sql_barras);

$meses = [];
$entradas = [];
$saidas = [];

while ($row = $stmtBarras->fetch(PDO::FETCH_ASSOC)) {
    $meses[]    = ucfirst($row['mes']); // 'janeiro', 'fevereiro' etc.
    $entradas[] = (float)$row['total_receita'];
    $saidas[]   = (float)$row['total_despesa'];
}

// 3) Consulta para gráfico de pizza (ex: despesas por categoria)
$sql_pizza = "
    SELECT c.categoria, SUM(t.valor) AS total
    FROM transacoes t
    JOIN categorias c ON t.categoria_id = c.id
    WHERE t.tipo = 'Despesa'
    GROUP BY c.categoria
    ORDER BY total DESC
";
$stmtPizza = $pdo->query($sql_pizza);

$categorias = [];
$valores = [];

while ($row = $stmtPizza->fetch(PDO::FETCH_ASSOC)) {
    $categorias[] = $row['categoria'];
    $valores[]    = (float)$row['total'];
}

// 4) Montar JSON
$data = [
    // Indicadores
    'total_entradas' => (float)$indic['total_entradas'],
    'total_saidas'   => (float)$indic['total_saidas'],
    'saldo_atual'    => (float)$saldo_atual,
    'total_vencer'   => (float)$indic['total_vencer'],

    // Dados de barras
    'meses'    => $meses,
    'entradas' => $entradas,
    'saidas'   => $saidas,

    // Dados de pizza
    'categorias' => $categorias,
    'valores'    => $valores
];

echo json_encode($data);
