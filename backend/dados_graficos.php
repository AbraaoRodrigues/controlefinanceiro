<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");
include_once "../includes/conexao.php";

$pdo->query("SET lc_time_names = 'pt_BR'");

// Filtro de data (caso esteja usando):
$dataInicio = isset($_GET['inicio']) ? $_GET['inicio'] : '';
$dataFim    = isset($_GET['fim'])    ? $_GET['fim'] : '';

/** 1) Montar WHERE principal **/
$whereSQL = "WHERE 1=1";
if (!empty($dataInicio)) {
    $whereSQL .= " AND t.data >= :dataInicio";
}
if (!empty($dataFim)) {
    $whereSQL .= " AND t.data <= :dataFim";
}

/** 2) Indicadores (Entradas, Saídas, etc.) **/
$sql_indic = "
    SELECT 
       SUM(CASE WHEN t.tipo = 'Receita' THEN t.valor ELSE 0 END) AS total_entradas,
       SUM(CASE WHEN t.tipo = 'Despesa' THEN t.valor ELSE 0 END) AS total_saidas,
       SUM(CASE WHEN t.status = 'Pendente' AND t.data >= CURDATE() THEN t.valor ELSE 0 END) AS total_vencer,
       SUM(CASE WHEN t.status = 'Pendente' AND t.data < CURDATE()  THEN t.valor ELSE 0 END) AS total_vencido
    FROM transacoes t
    $whereSQL
";
$stmtInd = $pdo->prepare($sql_indic);
// bindValue se tiver dataInicio/dataFim
if (!empty($dataInicio)) $stmtInd->bindValue(':dataInicio', $dataInicio);
if (!empty($dataFim))    $stmtInd->bindValue(':dataFim', $dataFim);
$stmtInd->execute();

$indic = $stmtInd->fetch(PDO::FETCH_ASSOC);

$total_entradas = (float)$indic['total_entradas'];
$total_saidas   = (float)$indic['total_saidas'];
$total_vencer   = (float)$indic['total_vencer'];
$total_vencido  = (float)$indic['total_vencido'];

$saldo_atual    = $total_entradas - $total_saidas;

/** 3) Lista de próximos vencimentos (transações pendentes e data >= hoje) **/
$sql_proximos = "
    SELECT t.id, t.data, t.descricao, t.valor, t.status
    FROM transacoes t
    $whereSQL
    AND t.status = 'Pendente'
    AND t.data >= CURDATE()
    ORDER BY t.data ASC
    LIMIT 10
";
$stmtProx = $pdo->prepare($sql_proximos);
if (!empty($dataInicio)) $stmtProx->bindValue(':dataInicio', $dataInicio);
if (!empty($dataFim))    $stmtProx->bindValue(':dataFim', $dataFim);
$stmtProx->execute();

$proximos = [];
while ($row = $stmtProx->fetch(PDO::FETCH_ASSOC)) {
    // Formatando data (opcional) ou manda crua
    $row['data'] = date('d/m/Y', strtotime($row['data']));

    // Pode também formatar valor aqui, ou formatar em JS
    // $row['valor'] = number_format($row['valor'], 2, ',', '.');

    $proximos[] = $row;
}

/** 4) Dados do gráfico de barras **/
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

/** 5) Dados do gráfico de pizza (despesas por categoria) **/
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

/** 6) Monta o objeto final e retorna JSON **/
$dataOut = [
    // Indicadores
    'total_entradas' => $total_entradas,
    'total_saidas'   => $total_saidas,
    'saldo_atual'    => $saldo_atual,
    'total_vencer'   => $total_vencer,
    'total_vencido'  => $total_vencido,

    // BARRAS
    'meses'    => $meses,
    'entradas' => $entradas,
    'saidas'   => $saidas,

    // PIZZA
    'categorias' => $categorias,
    'valores'    => $valores,

    // LISTA de próximos vencimentos
    'proximos' => $proximos
];

echo json_encode($dataOut);
