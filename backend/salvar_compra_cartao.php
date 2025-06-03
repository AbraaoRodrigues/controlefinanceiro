<?php
session_start();
require_once '../includes/conexao.php';
require_once '../funcoes/fatura_utils.php';

header("Content-Type: application/json");

// Sessão de teste (remova em produção)
if (!isset($_SESSION['usuario_id'])) {
  $_SESSION['usuario_id'] = 1;
}

$usuario_id   = $_SESSION['usuario_id'];
$cartao_id    = $_POST['cartao_id'] ?? null;
$descricao    = $_POST['descricao'] ?? '';
$valor_total  = str_replace([',', 'R$', ' '], ['.', '', ''], $_POST['valor_total'] ?? 0);
$parcelas     = intval($_POST['parcelas'] ?? 1);
$data_compra  = $_POST['data_compra'] ?? date('Y-m-d');

if (!$cartao_id || !$descricao || !$valor_total || $parcelas <= 0) {
  echo json_encode(["success" => false, "message" => "Todos os campos são obrigatórios."]);
  exit;
}

try {
  $pdo->beginTransaction();

  // Insere a compra
  $stmt = $pdo->prepare("INSERT INTO compras_cartao (cartao_id, descricao, valor_total, parcelas, data_compra)
                         VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$cartao_id, $descricao, $valor_total, $parcelas, $data_compra]);
  $compra_id = $pdo->lastInsertId();

  // Insere as parcelas
  $valor_parcela = round($valor_total / $parcelas, 2);
  $stmtParcela = $pdo->prepare("INSERT INTO parcelas_cartao (compra_id, numero, valor, vencimento)
                                VALUES (?, ?, ?, ?)");

  for ($i = 0; $i < $parcelas; $i++) {
    $vencimento = date('Y-m-d', strtotime("+$i month", strtotime($data_compra)));
    $stmtParcela->execute([$compra_id, $i + 1, $valor_parcela, $vencimento]);
  }

  // Atualiza a fatura e o lançamento
  $referencia = date('Y-m-01', strtotime($data_compra));
  $fatura_id = buscarFaturaPorCartaoEMes($pdo, $cartao_id, $referencia);

  if ($fatura_id) {
    atualizarValorLancamentoDaFatura($pdo, $fatura_id);
  }

  $pdo->commit();
  echo json_encode(["success" => true, "message" => "Compra registrada com sucesso!"]);
} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
