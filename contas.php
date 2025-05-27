<?php include 'includes/header.php'; ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lançamentos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <link rel="stylesheet" href="css/estilo_base.css">
</head>
<main class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Contas Bancárias</h2>
    <button id="btnNovaConta" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalConta">
      Nova Conta
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead class="table-dark">
        <tr>
          <th>Conta</th>
          <th>Banco</th>
          <th>Saldo Inicial</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody id="tabela-contas">
        <tr>
          <td colspan="5" class="text-center">Carregando...</td>
        </tr>
      </tbody>
    </table>
  </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modalConta" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-conta">
        <input type="hidden" id="id" name="id">
        <div class="modal-header">
          <h5 class="modal-title">Conta Bancária</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nome da Conta</label>
            <input type="text" class="form-control" id="nome_conta" name="nome_conta" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Banco</label>
            <input type="text" class="form-control" id="banco" name="banco" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Saldo Inicial</label>
            <input type="text" step="0.01" class="form-control" id="saldo_inicial" name="saldo_inicial" value="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="js/contas.js"></script>
