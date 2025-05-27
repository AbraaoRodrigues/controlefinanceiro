<?php
require_once 'includes/header.php';
?>

<body>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_base.css">

  <main class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Registrar Compra no Cartão</h2>
    </div>

    <form id="form-compra-cartao">
      <div class="mb-3">
        <label for="cartao_id" class="form-label">Cartão</label>
        <select class="form-select" id="cartao_id" name="cartao_id" required>
          <option value="">Selecione</option>
          <?php
          require_once 'includes/conexao.php';
          $stmt = $pdo->prepare("SELECT id, nome_cartao FROM cartoes WHERE usuario_id = ? AND status = 'Ativo'");
          $stmt->execute([$_SESSION['usuario_id']]);
          while ($cartao = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$cartao['id']}'>" . htmlspecialchars($cartao['nome_cartao']) . "</option>";
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="descricao" class="form-label">Descrição</label>
        <input type="text" class="form-control" id="descricao" name="descricao" required>
      </div>

      <div class="mb-3">
        <label for="valor_total" class="form-label">Valor Total</label>
        <input type="text" class="form-control" id="valor_total" name="valor_total" required>
      </div>

      <div class="mb-3">
        <label for="parcelas" class="form-label">Parcelas</label>
        <input type="number" class="form-control" id="parcelas" name="parcelas" value="1" min="1" required>
      </div>

      <div class="mb-3">
        <label for="data_compra" class="form-label">Data da Compra</label>
        <input type="date" class="form-control" id="data_compra" name="data_compra" required>
      </div>

      <button type="submit" class="btn btn-primary">Registrar Compra</button>
    </form>
  </main>
  <script src="js/registro_compra_cartao.js"></script>

</body>
<?php require_once 'includes/footer.php'; ?>
