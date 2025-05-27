<?php
require_once 'includes/header.php';
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lançamentos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_base.css">
</head>

<body class="d-flex flex-column min-vh-100">
  <main class="container my-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Cartões</h2>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCartao">Novo Cartão</button>
    </div>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>Nome do Cartão</th>
            <th>Bandeira</th>
            <th>Limite</th>
            <th>Fechamento</th>
            <th>Vencimento</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tabela-cartoes">
          <!-- Conteúdo será carregado via JS -->

        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal de Novo Cartão -->
  <div class="modal fade" id="modalCartao" tabindex="-1" aria-labelledby="modalCartaoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form class="modal-content" id="form-cartao">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCartaoLabel">Cadastrar Novo Cartão</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <input type="hidden" id="cartao_id" name="cartao_id">

        <div class="modal-body">
          <div class="mb-3">
            <label for="nome_cartao" class="form-label">Nome do Cartão</label>
            <input type="text" class="form-control" id="nome_cartao" name="nome_cartao" required>
          </div>
          <div class="mb-3">
            <label for="bandeira" class="form-label">Bandeira</label>
            <input type="text" class="form-control" id="bandeira" name="bandeira">
          </div>
          <div class="mb-3">
            <label for="limite" class="form-label">Limite</label>
            <input type="text" class="form-control" id="limite" name="limite" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="fechamento" class="form-label">Fechamento</label>
              <input type="number" class="form-control" id="fechamento" name="fechamento" min="1" max="31" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="vencimento" class="form-label">Vencimento</label>
              <input type="number" class="form-control" id="vencimento" name="vencimento" min="1" max="31" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
              <option value="Ativo" selected>Ativo</option>
              <option value="Inativo">Inativo</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <?php require_once 'includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/registro_cartao.js"></script>
  <script src="js/carregar_cartoes.js"></script>
  <script src="js/editar_cartao.js"></script>
  <!--<script src="js/cartao_excluir_arquivar.js"></script>-->

</body>

</html>
