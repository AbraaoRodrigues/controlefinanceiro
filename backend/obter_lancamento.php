<?php
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Sessão expirada."]);
  exit;
}

$id = $_GET['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$id) {
  echo json_encode(["success" => false, "message" => "ID do lançamento não informado."]);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT * FROM lancamentos WHERE id = :id AND usuario_id = :usuario_id");
  $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);
  $lancamento = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($lancamento) {
    echo json_encode(["success" => true, "dados" => $lancamento]);
  } else {
    echo json_encode(["success" => false, "message" => "Lançamento não encontrado."]);
  }
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
