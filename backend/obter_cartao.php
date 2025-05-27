<?php
session_start();
require_once '../includes/conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['success' => false, 'message' => 'Sessão expirada.']);
  exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
  echo json_encode(['success' => false, 'message' => 'ID não informado.']);
  exit;
}

try {
  $stmt = $pdo->prepare("SELECT * FROM cartoes WHERE id = :id AND usuario_id = :usuario_id");
  $stmt->execute([
    ':id' => $id,
    ':usuario_id' => $_SESSION['usuario_id']
  ]);

  $cartao = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($cartao) {
    echo json_encode(['success' => true, 'cartao' => $cartao]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Cartão não encontrado.']);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
