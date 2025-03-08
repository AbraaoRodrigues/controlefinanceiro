<?php
header("Content-Type: application/json");
include("../includes/conexao.php"); // Ajuste o caminho conforme necessário

// 1️⃣ Consultar entradas e saídas por mês
$sql_barras = "SELECT DATE_FORMAT(data, '%M') AS mes, 
               SUM(CASE WHEN tipo = 'Receita' THEN valor ELSE 0 END) AS Receita,
               SUM(CASE WHEN tipo = 'Despesa' THEN valor ELSE 0 END) AS Despesa
        FROM transacoes
        GROUP BY mes
        ORDER BY STR_TO_DATE(mes, '%M')";

$result_barras = $conn->query($sql_barras);
$meses = [];
$entradas = [];
$saidas = [];

while ($row = $result_barras->fetch_assoc()) {
    $meses[] = ucfirst($row["mes"]); // Capitalizar o nome do mês
    $entradas[] = (float)$row["entradas"];
    $saidas[] = (float)$row["saidas"];
}

// 2️⃣ Consultar categorias de gastos para o gráfico de pizza
$sql_pizza = "SELECT categoria, SUM(valor) AS total 
              FROM categorias 
              WHERE tipo = 'Receita' 
              GROUP BY categoria";

$result_pizza = $conn->query($sql_pizza);
$categorias = [];
$valores = [];

while ($row = $result_pizza->fetch_assoc()) {
    $categorias[] = $row["categoria"];
    $valores[] = (float)$row["total"];
}

// Retornar os dados em JSON
echo json_encode([
    "meses" => $meses,
    "entradas" => $entradas,
    "saidas" => $saidas,
    "categorias" => $categorias,
    "valores" => $valores
]);

$conn->close();
?>
