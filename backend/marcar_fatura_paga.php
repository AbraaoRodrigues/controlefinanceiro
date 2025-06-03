<?php
require_once 'includes/conexao.php';

$fatura_id = $_POST['fatura_id'] ?? null;

if (!$fatura_id) {
  echo json_encode(['erro' => 'ID inválido']);
  exit;
}

// Busca fatura
$stmt = $pdo->prepare("SELECT lancamento_id FROM faturas_cartao WHERE id = ?");
$stmt->execute([$fatura_id]);
$fatura = $stmt->fetch();

if (!$fatura) {
  echo json_encode(['erro' => 'Fatura não encontrada']);
  exit;
}

// Atualiza fatura
$pdo->prepare("UPDATE faturas_cartao SET status = 'Paga' WHERE id = ?")->execute([$fatura_id]);

// Atualiza lançamento
if ($fatura['lancamento_id']) {
  $pdo->prepare("UPDATE lancamentos SET status = 'Pago' WHERE id = ?")->execute([$fatura['lancamento_id']]);
}

echo json_encode(['sucesso' => true]);
