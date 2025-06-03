<?php
require_once __DIR__ . '/../includes/conexao.php';
file_put_contents('debug.txt', "GET: " . json_encode($_GET) . "\n", FILE_APPEND);

$cartao_id = $_GET['cartao_id'] ?? null;
$mes = $_GET['mes'] ?? date('Y-m');

if (!$cartao_id) {
  echo json_encode(['erro' => 'Cartão inválido']);
  exit;
}

$mesReferencia = date('Y-m-01', strtotime($mes));

// Verifica se já existe fatura
$stmt = $pdo->prepare("SELECT * FROM faturas_cartao WHERE cartao_id = ? AND referencia = ?");
$stmt->execute([$cartao_id, $mesReferencia]);
$fatura = $stmt->fetch();

// Se não existir, tenta gerar
if (!$fatura) {
  // Busca parcelas do mês
  $parcelas = $pdo->prepare("
        SELECT c.*,
               FLOOR(DATEDIFF(:ref, c.data_compra) / 30) + 1 AS parcela_atual,
               (c.valor_total / c.parcelas) AS valor_parcela
        FROM compras_cartao c
        WHERE c.cartao_id = :cartao_id
          AND :ref BETWEEN c.data_compra AND DATE_ADD(c.data_compra, INTERVAL (c.parcelas - 1) MONTH)
    ");
  $parcelas->execute([
    ':cartao_id' => $cartao_id,
    ':ref' => $mesReferencia
  ]);
  $parcelas = $parcelas->fetchAll();

  if (count($parcelas) > 0) {
    $total_fatura = 0;
    foreach ($parcelas as $p) {
      $total_fatura += $p['valor_parcela'];
    }

    // Criar lançamento
    $descricao = "Fatura Cartão #$cartao_id - " . date('m/Y', strtotime($mesReferencia));
    $insLanc = $pdo->prepare("
            INSERT INTO lancamentos (tipo, descricao, valor, data, status, origem)
            VALUES ('Despesa', :descricao, :valor, LAST_DAY(:data), 'Pendente', 'Cartao')
        ");
    $insLanc->execute([
      ':descricao' => $descricao,
      ':valor' => $total_fatura,
      ':data' => $mesReferencia
    ]);
    $lancamento_id = $pdo->lastInsertId();

    // Criar fatura
    $insFat = $pdo->prepare("
            INSERT INTO faturas_cartao (cartao_id, referencia, valor_total, status, lancamento_id)
            VALUES (:cartao_id, :ref, :total, 'Aberta', :lancamento_id)
        ");
    $insFat->execute([
      ':cartao_id' => $cartao_id,
      ':ref' => $mesReferencia,
      ':total' => $total_fatura,
      ':lancamento_id' => $lancamento_id
    ]);

    // Atualiza fatura carregada
    $stmt = $pdo->prepare("SELECT * FROM faturas_cartao WHERE cartao_id = ? AND referencia = ?");
    $stmt->execute([$cartao_id, $mesReferencia]);
    $fatura = $stmt->fetch();
  }
}

// Se mesmo assim não tiver fatura (sem parcelas no mês), retorna nulo
if (!$fatura) {
  echo json_encode(['fatura' => null]);
  exit;
}

// Agora busca parcelas da fatura gerada
$stmt = $pdo->prepare("
    SELECT cc.descricao, cc.valor_total, cc.parcelas, cc.data_compra,
           (cc.valor_total / cc.parcelas) AS valor_parcela
    FROM compras_cartao cc
    WHERE cc.cartao_id = ?
      AND ? BETWEEN cc.data_compra AND DATE_ADD(cc.data_compra, INTERVAL (cc.parcelas - 1) MONTH)
");
$stmt->execute([$cartao_id, $mesReferencia]);
$parcelas = $stmt->fetchAll();

echo json_encode([
  'fatura' => $fatura,
  'parcelas' => $parcelas
]);
