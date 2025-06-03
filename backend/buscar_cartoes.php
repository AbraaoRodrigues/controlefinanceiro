<?php
session_start();
require_once '../includes/conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
  $_SESSION['usuario_id'] = 1; // 👈 para testes
}


if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['success' => false, 'message' => 'Sessão expirada.']);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];

try {
  $stmt = $pdo->prepare("SELECT * FROM cartoes WHERE usuario_id = :id ORDER BY nome_cartao");
  $stmt->execute([':id' => $usuario_id]);
  $cartoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'dados' => $cartoes]);
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Erro ao buscar: ' . $e->getMessage()]);
}
