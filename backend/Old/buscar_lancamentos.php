<?php
<<<<<<< HEAD
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  $_SESSION['usuario_id'] = 1; // fallback para testes locais
}
$usuario_id = $_SESSION['usuario_id'];

// Filtros opcionais
$dataInicio = $_GET['inicio'] ?? null;
$dataFim    = $_GET['fim'] ?? null;
$categoria  = $_GET['categoria'] ?? null;
$tipo       = $_GET['tipo'] ?? null;
$status     = $_GET['status'] ?? null;

$where = ["l.usuario_id = :usuario_id"];
$params = [":usuario_id" => $usuario_id];

if ($dataInicio) {
  $where[] = "l.data >= :dataInicio";
  $params[':dataInicio'] = $dataInicio;
}
if ($dataFim) {
  $where[] = "l.data <= :dataFim";
  $params[':dataFim'] = $dataFim;
}
if ($categoria) {
  $where[] = "l.categoria_id = :categoria";
  $params[':categoria'] = $categoria;
}
if ($tipo) {
  $where[] = "l.tipo = :tipo";
  $params[':tipo'] = $tipo;
}
if ($status) {
  $where[] = "l.status = :status";
  $params[':status'] = $status;
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

try {
  $sql = "
    SELECT
      l.id, l.tipo, l.valor, l.descricao, l.data, l.status,
      c.categoria
    FROM lancamentos l
    LEFT JOIN categorias c ON l.categoria_id = c.id
    $whereSQL
    ORDER BY l.data DESC
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(["success" => true, "dados" => $dados]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
=======
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
>>>>>>> 0e975c84fd4b26b8c215b07fa060018ab3c4ba9a
