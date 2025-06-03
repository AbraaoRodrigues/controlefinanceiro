<?php
require_once 'includes/conexao.php';
require_once 'backend/gerar_fatura_mensal.php';
require_once 'funcoes/fatura_utils.php';

$mesAtual = date('Y-m-01');

$cartoes = $pdo->query("SELECT id FROM cartoes WHERE status = 'Ativo'")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cartoes as $c) {
  $fatura_id = buscarFaturaPorCartaoEMes($pdo, $c['id'], $mesAtual);
  if ($fatura_id) {
    atualizarValorLancamentoDaFatura($pdo, $fatura_id);
  }
}

// Geração automática da fatura (com verificação)
$mesAtual = date('Y-m-01');
gerarFaturasDoMes($pdo, $mesAtual);
?>

<?php if (!empty($mensagem_fatura)): ?>
  <div id="alert-fatura" class="alert alert-success">
    <?= $mensagem_fatura ?>
  </div>
  <script>
    setTimeout(() => {
      const alerta = document.getElementById('alert-fatura');
      if (alerta) {
        alerta.style.transition = 'opacity 0.5s ease';
        alerta.style.opacity = 0;
        setTimeout(() => alerta.remove(), 500);
      }
    }, 3000);
  </script>
<?php endif; ?>

<h2 class="mb-4">Resumo Financeiro</h2>
<div class="row g-3">
  <div class="col-md-3">
    <div class="card border-success shadow-sm">
      <div class="card-body text-success">
        <h5 class="card-title">Entradas</h5>
        <p class="fs-4 fw-bold">R$ 0,00</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-danger shadow-sm">
      <div class="card-body text-danger">
        <h5 class="card-title">Saídas</h5>
        <p class="fs-4 fw-bold">R$ 0,00</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-primary shadow-sm">
      <div class="card-body text-primary">
        <h5 class="card-title">Saldo</h5>
        <p class="fs-4 fw-bold">R$ 0,00</p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-warning shadow-sm">
      <div class="card-body text-warning">
        <h5 class="card-title">Pendentes</h5>
        <p class="fs-4 fw-bold">R$ 0,00</p>
      </div>
    </div>
  </div>
</div>
