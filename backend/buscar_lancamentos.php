<?php
include '../includes/conexao.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Captura os filtros enviados via GET ou POST
    $categoria = $_GET['categoria'] ?? '';
    $dataInicio = $_GET['dataInicio'] ?? '';
    $dataFim = $_GET['dataFim'] ?? '';
    $valorMin = $_GET['valorMin'] ?? '';
    $valorMax = $_GET['valorMax'] ?? '';
    $status = $_GET['status'] ?? '';

    // Monta a query com os filtros opcionais
    $sql = "
        SELECT 
            t.id, 
            t.tipo, 
            t.valor, 
            t.descricao, 
            t.data, 
            t.status, 
            c.categoria AS categoria
        FROM transacoes t
        LEFT JOIN categorias c ON t.categoria_id = c.id
        WHERE 1 = 1
    ";

    // Array para armazenar os parâmetros
    $params = [];

    // Aplica os filtros se fornecidos
    if (!empty($categoria)) {
        $sql .= " AND c.id = :categoria";
        $params[':categoria'] = $categoria;
    }

    if (!empty($dataInicio)) {
        $sql .= " AND t.data >= :dataInicio";
        $params[':dataInicio'] = $dataInicio;
    }

    if (!empty($dataFim)) {
        $sql .= " AND t.data <= :dataFim";
        $params[':dataFim'] = $dataFim;
    }

    if (!empty($valorMin)) {
        $sql .= " AND t.valor >= :valorMin";
        $params[':valorMin'] = $valorMin;
    }

    if (!empty($valorMax)) {
        $sql .= " AND t.valor <= :valorMax";
        $params[':valorMax'] = $valorMax;
    }

    if (!empty($status)) {
        $sql .= " AND t.status = :status";
        $params[':status'] = $status;
    }

    // Ordenação
    $sql .= " ORDER BY t.data DESC";
    
    error_log("SQL Gerado: " . $sql);
error_log("Parâmetros: " . print_r($params, true));


    // Prepara e executa a query
    $query = $pdo->prepare($sql);
    $query->execute($params);
    $lancamentos = $query->fetchAll(PDO::FETCH_ASSOC);

    // Retorna os resultados em JSON
    header('Content-Type: application/json');
    error_log("Lançamentos encontrados: " . print_r($lancamentos, true));

    echo json_encode($lancamentos);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => $e->getMessage()]);
}
?>
