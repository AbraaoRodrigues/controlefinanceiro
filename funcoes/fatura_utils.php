<?php
require_once __DIR__ . '/../includes/conexao.php';

/**
 * Busca o ID da fatura de um cartão para um mês específico
 */
function buscarFaturaPorCartaoEMes(PDO $pdo, int $cartao_id, string $referencia): ?int
{
  $stmt = $pdo->prepare("SELECT id FROM faturas_cartao WHERE cartao_id = ? AND referencia = ?");
  $stmt->execute([$cartao_id, $referencia]);
  $fatura = $stmt->fetch(PDO::FETCH_ASSOC);
  return $fatura ? (int)$fatura['id'] : null;
  error_log("Fatura encontrada ID: " . $fatura_id);
}

/**
 * Atualiza o valor total da fatura e do lançamento associado
 */
function atualizarValorLancamentoDaFatura(PDO $pdo, int $fatura_id): void
{
  // Busca dados da fatura
  $fatura = $pdo->prepare("SELECT cartao_id, referencia, lancamento_id FROM faturas_cartao WHERE id = ?");
  $fatura->execute([$fatura_id]);
  $dados = $fatura->fetch(PDO::FETCH_ASSOC);
  if (!$dados) return;

  $cartao_id = $dados['cartao_id'];
  $referencia = $dados['referencia'];
  $lancamento_id = $dados['lancamento_id'];

  // Soma das parcelas válidas no mês da fatura
  $stmt = $pdo->prepare("
    SELECT SUM(valor_total / parcelas) AS total
    FROM compras_cartao
    WHERE cartao_id = :cartao_id
      AND :ref BETWEEN data_compra AND DATE_ADD(data_compra, INTERVAL (parcelas - 1) MONTH)
  ");
  $stmt->execute([
    ':cartao_id' => $cartao_id,
    ':ref' => $referencia
  ]);
  $valor_total = $stmt->fetchColumn() ?: 0;

  // Atualiza fatura
  $pdo->prepare("UPDATE faturas_cartao SET valor_total = ? WHERE id = ?")
    ->execute([$valor_total, $fatura_id]);

  // Buscar usuario_id do cartão
  $stmt = $pdo->prepare("SELECT usuario_id FROM cartoes WHERE id = ?");
  $stmt->execute([$cartao_id]);
  $usuario_id = $stmt->fetchColumn();

  // Atualiza valor e usuário do lançamento vinculado
  if ($lancamento_id) {
    $pdo->prepare("UPDATE lancamentos SET valor = ?, usuario_id = ? WHERE id = ?")
      ->execute([$valor_total, $usuario_id, $lancamento_id]);
  }
}
