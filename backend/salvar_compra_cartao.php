<?php
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Sessão expirada ou inválida."]);
  exit;
}

$cartao_id    = $_POST['cartao_id'] ?? null;
$descricao    = $_POST['descricao'] ?? '';
$valor_total  = str_replace([',', 'R$', ' '], ['.', '', ''], $_POST['valor_total'] ?? 0);
$parcelas     = intval($_POST['parcelas'] ?? 1);
$data_compra  = $_POST['data_compra'] ?? date('Y-m-d');

if (!$cartao_id || !$descricao || !$valor_total || !$parcelas) {
  echo json_encode(["success" => false, "message" => "Todos os campos são obrigatórios."]);
  exit;
}

try {
  $pdo->beginTransaction();

  // Salva a compra principal
  $stmt = $pdo->prepare("INSERT INTO compras_cartao (cartao_id, descricao, valor_total, parcelas, data_compra)
        VALUES (:cartao_id, :descricao, :valor_total, :parcelas, :data_compra)");

  $stmt->execute([
    ':cartao_id'   => $cartao_id,
    ':descricao'   => $descricao,
    ':valor_total' => $valor_total,
    ':parcelas'    => $parcelas,
    ':data_compra' => $data_compra
  ]);

  $compra_id = $pdo->lastInsertId();
  $valor_parcela = round($valor_total / $parcelas, 2);

  // Gera as parcelas
  for ($i = 0; $i < $parcelas; $i++) {
    $vencimento = date('Y-m-d', strtotime("+$i month", strtotime($data_compra)));

    $stmtParcela = $pdo->prepare("INSERT INTO parcelas_cartao (compra_id, numero, valor, vencimento)
            VALUES (:compra_id, :numero, :valor, :vencimento)");

    $stmtParcela->execute([
      ':compra_id' => $compra_id,
      ':numero'    => $i + 1,
      ':valor'     => $valor_parcela,
      ':vencimento' => $vencimento
    ]);
  }

  $pdo->commit();
  echo json_encode(["success" => true, "message" => "Compra registrada com sucesso!"]);
} catch (Exception $e) {
  $pdo->rollBack();
  echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
