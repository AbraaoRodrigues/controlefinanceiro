<?php
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
