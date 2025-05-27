<?php
require_once '../includes/conexao.php'; // ajuste o caminho conforme a estrutura do seu projeto

function gerarFaturasDoMes($pdo, $mesAno)
{
  $dataRef = date('Y-m-01', strtotime($mesAno));

  // Obtem cartões ativos
  $cartoes = $pdo->query("SELECT id FROM cartoes WHERE status = 'Ativo'")->fetchAll(PDO::FETCH_ASSOC);

  foreach ($cartoes as $cartao) {
    $cartao_id = $cartao['id'];

    // Busca parcelas de compras válidas para esse mês
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

    // Criação do lançamento
    $descricao = "Fatura Cartão #$cartao_id - " . date('m/Y', strtotime($dataRef));
    $insLanc = $pdo->prepare("
            INSERT INTO lancamentos (tipo, descricao, valor, data, status, origem)
            VALUES ('Despesa', :descricao, :valor, LAST_DAY(:data), 'Pendente', 'Cartao')
        ");
    $insLanc->execute([
      ':descricao' => $descricao,
      ':valor' => $total_fatura,
      ':data' => $dataRef
    ]);
    $lancamento_id = $pdo->lastInsertId();

    // Criação da fatura
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
  }

  echo "✅ Faturas geradas para o mês: " . date('m/Y', strtotime($dataRef));
}

// Execução do script
$mes = $_GET['mes'] ?? date('Y-m-01'); // permite execução via URL, ex: gerar_faturas_do_mes.php?mes=2025-06-01
gerarFaturasDoMes($pdo, $mes);
