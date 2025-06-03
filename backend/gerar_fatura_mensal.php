<?php
require_once 'includes/conexao.php';

function gerarFaturasDoMes($pdo, $mesAno)
{
  $dataRef = date('Y-m-01', strtotime($mesAno));
  $cartoes = $pdo->query("SELECT id FROM cartoes WHERE status = 'Ativo'")->fetchAll(PDO::FETCH_ASSOC);

  $mensagem = '';

  foreach ($cartoes as $cartao) {
    $cartao_id = $cartao['id'];

    // Verifica se já existe fatura
    $check = $pdo->prepare("SELECT COUNT(*) FROM faturas_cartao WHERE cartao_id = :cartao_id AND referencia = :ref");
    $check->execute([
      ':cartao_id' => $cartao_id,
      ':ref' => $dataRef
    ]);
    if ($check->fetchColumn() > 0) continue;

    // Busca parcelas da fatura
    $stmt = $pdo->prepare("
      SELECT c.*,
             FLOOR(DATEDIFF(:ref, c.data_compra) / 30) + 1 AS parcela_atual,
             c.valor_total / c.parcelas AS valor_parcela
      FROM compras_cartao c
      WHERE c.cartao_id = :cartao_id
        AND :ref BETWEEN c.data_compra AND DATE_ADD(c.data_compra, INTERVAL (c.parcelas - 1) MONTH)
    ");
    $stmt->execute([
      ':cartao_id' => $cartao_id,
      ':ref' => $dataRef
    ]);

    $parcelas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($parcelas) === 0) continue;

    $total_fatura = 0;
    foreach ($parcelas as $p) {
      $total_fatura += $p['valor_parcela'];
    }

    // Criar lançamento
    $descricao = "Fatura Cartão #$cartao_id - " . date('m/Y', strtotime($dataRef));
    $insLanc = $pdo->prepare("
      INSERT INTO lancamentos (usuario_id, tipo, descricao, valor, data, status, origem)
      VALUES (:usuario_id, 'Despesa', :descricao, :valor, LAST_DAY(:data), 'Pendente', 'Cartao')
    ");
    $insLanc->execute([
      ':usuario_id' => $usuario_id, // deve ser passado como parâmetro
      ':descricao' => $descricao,
      ':valor' => $total_fatura,
      ':data' => $dataRef
    ]);
    $lancamento_id = $pdo->lastInsertId();

    // Criar fatura
    $insFat = $pdo->prepare("
      INSERT INTO faturas_cartao (cartao_id, referencia, valor_total, status, lancamento_id)
      VALUES (:cartao_id, :ref, :total, 'Aberta', :lancamento_id)
    ");
    $insFat->execute([
      ':cartao_id' => $cartao_id,
      ':ref' => $dataRef,
      ':total' => $total_fatura,
      ':lancamento_id' => $lancamento_id
    ]);

    // Adiciona à mensagem
    $mensagem .= "<span class='me-2'>✅ Fatura gerada para o mês: " . date('m/Y', strtotime($dataRef)) . " (Cartão #$cartao_id)</span>";
  }

  // Armazena mensagem global
  if (!empty($mensagem)) {
    $GLOBALS['mensagem_fatura'] = $mensagem;
  }
}

// Execução direta apenas se acessar pela URL
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
  $mes = $_GET['mes'] ?? date('Y-m-01');
  gerarFaturasDoMes($pdo, $mes);
}
