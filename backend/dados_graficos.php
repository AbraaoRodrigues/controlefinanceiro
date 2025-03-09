<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
include_once "../includes/conexao.php";

$pdo->query("SET lc_time_names = 'pt_BR'");

// Recebe os parâmetros de data via GET
$dataInicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
$dataFim    = isset($_GET['fim'])    ? $_GET['fim'] : '';

// Monta where partindo de WHERE 1=1
$whereSQL = "WHERE 1=1";
if (!empty($dataInicio)) {
    $whereSQL .= " AND t.data >= :dataInicio";
}
if (!empty($dataFim)) {
    $whereSQL .= " AND t.data <= :dataFim";
}

// 1) Consulta para indicadores
// Aqui, exemplificando, mas usando subqueries e levando em conta o $whereSQL:

$sql_indicadores = "
    SELECT 
       SUM(CASE WHEN t.tipo = 'Receita' THEN t.valor ELSE 0 END) AS total_entradas,
       SUM(CASE WHEN t.tipo = 'Despesa' THEN t.valor ELSE 0 END) AS total_saidas,
       SUM(CASE WHEN t.status = 'Pendente' THEN t.valor ELSE 0 END) AS total_vencer
    FROM transacoes t
    $whereSQL
";
$stmtInd = $pdo->prepare($sql_indicadores);
if (!empty($dataInicio)) $stmtInd->bindValue(':dataInicio', $dataInicio);
if (!empty($dataFim))    $stmtInd->bindValue(':dataFim', $dataFim);
$stmtInd->execute();

$indic = $stmtInd->fetch(PDO::FETCH_ASSOC);

$total_entradas = (float)$indic['total_entradas'];
$total_saidas   = (float)$indic['total_saidas'];
$total_vencer   = (float)$indic['total_vencer'];
$saldo_atual    = $total_entradas - $total_saidas;


// 2) Consulta para gráfico de barras
$sql_barras = "
    SELECT 
        DATE_FORMAT(t.data, '%M') AS mes,
        SUM(CASE WHEN t.tipo = 'Receita' THEN t.valor ELSE 0 END) AS total_receita,
        SUM(CASE WHEN t.tipo = 'Despesa' THEN t.valor ELSE 0 END) AS total_despesa
    FROM transacoes t
    $whereSQL
    GROUP BY DATE_FORMAT(t.data, '%Y-%m')
    ORDER BY MIN(t.data)
";
$stmtBarras = $pdo->prepare($sql_barras);
if (!empty($dataInicio)) $stmtBarras->bindValue(':dataInicio', $dataInicio);
if (!empty($dataFim))    $stmtBarras->bindValue(':dataFim', $dataFim);

$stmtBarras->execute();


$meses = [];
$entradas = [];
$saidas = [];

while ($row = $stmtBarras->fetch(PDO::FETCH_ASSOC)) {
    $meses[]    = ucfirst($row['mes']);
    $entradas[] = (float)$row['total_receita'];
    $saidas[]   = (float)$row['total_despesa'];
}

// 3) Consulta para gráfico de pizza (ex: despesas por categoria)
$sql_pizza = "
    SELECT c.categoria, SUM(t.valor) AS total
    FROM transacoes t
    JOIN categorias c ON t.categoria_id = c.id
    $whereSQL
    AND t.tipo = 'Despesa'
    GROUP BY c.categoria
    ORDER BY total DESC
";
$stmtPizza = $pdo->prepare($sql_pizza);
if (!empty($dataInicio)) $stmtPizza->bindValue(':dataInicio', $dataInicio);
if (!empty($dataFim))    $stmtPizza->bindValue(':dataFim', $dataFim);

$stmtPizza->execute();


$categorias = [];
$valores = [];

while ($row = $stmtPizza->fetch(PDO::FETCH_ASSOC)) {
    $categorias[] = $row['categoria'];
    $valores[]    = (float)$row['total'];
}

// 4) Montar JSON final
$dataOut = [
    'total_entradas' => (float)$indic['total_entradas'],
    'total_saidas'   => (float)$indic['total_saidas'],
    'saldo_atual'    => (float)$saldo_atual,
    'total_vencer'   => (float)$indic['total_vencer'],
    'meses'          => $meses,
    'entradas'       => $entradas,
    'saidas'         => $saidas,
    'categorias'     => $categorias,
    'valores'        => $valores
];

echo json_encode($dataOut);
