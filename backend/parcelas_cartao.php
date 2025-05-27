<?php
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Sessão inválida."]);
  exit;
}

$cartao_id = $_GET['cartao_id'] ?? null;
if (!$cartao_id) {
  echo json_encode(["success" => false, "message" => "ID do cartão não informado."]);
  exit;
}

try {
  // Buscar compras e suas parcelas
  $stmt = $pdo->prepare("SELECT c.id AS compra_id, c.descricao, c.valor_total, c.parcelas, c.data_compra,
        p.numero, p.valor, p.vencimento, p.pago
        FROM compras_cartao c
        JOIN parcelas_cartao p ON p.compra_id = c.id
        WHERE c.cartao_id = :cartao_id
        ORDER BY c.data_compra, p.numero");

  $stmt->execute([':cartao_id' => $cartao_id]);
  $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(["success" => true, "dados" => $dados]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
