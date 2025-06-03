<?php
require_once 'includes/conexao.php';
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fatura do Cartão</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_base.css">
</head>

<body>
  <?php require_once 'includes/header.php'; ?>

  <main class="container my-4">
    <h2>Fatura do Cartão</h2>

    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label for="filtro_mes" class="form-label">Selecione o Mês</label>
        <input type="month" id="filtro_mes" name="filtro_mes" class="form-control" value="<?= date('Y-m') ?>">
      </div>

      <div class="col-md-6">
        <label for="filtro_cartao" class="form-label">Selecione o Cartão</label>
        <select class="form-select" id="filtro_cartao" name="filtro_cartao" required>
          <option value="">-- Escolha um cartão --</option>
          <?php
          $stmt = $pdo->prepare("SELECT id, nome_cartao FROM cartoes WHERE usuario_id = ? AND status = 'Ativo'");
          $stmt->execute([$_SESSION['usuario_id']]);
          while ($c = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$c['id']}'>" . htmlspecialchars($c['nome_cartao']) . "</option>";
          }
          ?>
        </select>
      </div>
    </div>

    <!-- 🔽 Este bloco é necessário para exibir os totais da fatura -->
    <div id="resumo-fatura" class="alert alert-light border d-flex justify-content-between align-items-center" style="display: none;">
      <span><strong>Total da Fatura:</strong> <span id="total-fatura">R$ 0,00</span></span>
      <span><strong>Valor Pendente:</strong> <span id="valor-pendente">R$ 0,00</span></span>
    </div>

    <div id="lista-compras">
      <!-- Aqui será preenchido dinamicamente via JS -->
    </div>
  </main>

  <?php require_once 'includes/footer.php'; ?>

  <script src="js/parcelas_cartao.js"></script>
</body>

</html>
