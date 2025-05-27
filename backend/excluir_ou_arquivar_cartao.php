<?php
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Sessão expirada."]);
  exit;
}

$id = $_POST['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$id) {
  echo json_encode(["success" => false, "message" => "ID do cartão não informado."]);
  exit;
}

try {
  // Recupera os dados completos do cartão
  $stmt = $pdo->prepare("SELECT * FROM cartoes WHERE id = ? AND usuario_id = ?");
  $stmt->execute([$id, $usuario_id]);
  $cartao = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$cartao) {
    echo json_encode(["success" => false, "message" => "Cartão não encontrado para este usuário."]);
    exit;
  }

  // Verifica se há compras vinculadas
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM compras_cartao WHERE cartao_id = ?");
  $stmt->execute([$id]);
  $tem_compras = $stmt->fetchColumn();

  if ($tem_compras > 0) {
    // Arquiva o cartão com data
    $stmt = $pdo->prepare("UPDATE cartoes SET status = 'Arquivado', data_arquivamento = NOW() WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);
    if ($stmt->rowCount()) {
      echo json_encode(["success" => true, "message" => "Cartão arquivado (existem compras vinculadas)."]);
    } else {
      echo json_encode(["success" => false, "message" => "Falha ao arquivar o cartão."]);
    }
  } else {
    // Exclui o cartão completamente
    $stmt = $pdo->prepare("DELETE FROM cartoes WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);
    if ($stmt->rowCount()) {
      echo json_encode(["success" => true, "message" => "Cartão excluído com sucesso."]);
    } else {
      echo json_encode(["success" => false, "message" => "Falha ao excluir o cartão."]);
    }
  }
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
