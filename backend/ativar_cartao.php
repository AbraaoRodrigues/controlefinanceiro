<?php
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  $_SESSION['usuario_id'] = 1; // Para testes sem login
}

$id = $_POST['id'] ?? null;
$usuario_id = $_SESSION['usuario_id'];

if (!$id) {
  echo json_encode(["success" => false, "message" => "ID do cartão não informado."]);
  exit;
}

try {
  // Atualiza o status do cartão para Ativo e limpa a data_arquivamento
  $stmt = $pdo->prepare("UPDATE cartoes SET status = 'Ativo', data_arquivamento = NULL WHERE id = ? AND usuario_id = ?");
  $stmt->execute([$id, $usuario_id]);

  if ($stmt->rowCount()) {
    echo json_encode(["success" => true, "message" => "Cartão reativado com sucesso."]);
  } else {
    echo json_encode(["success" => false, "message" => "Nenhuma alteração realizada. Verifique se o cartão já está ativo ou se pertence ao usuário."]);
  }
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
