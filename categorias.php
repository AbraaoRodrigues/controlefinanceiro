<?php include 'includes/header.php'; ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lançamentos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_base.css">
</head>

<main class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Categorias</h2>
    <button
      class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCategoria" id="btnNovaCategoria">Nova Categoria
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead class="table-dark">
        <tr>
          <th>Categoria</th>
          <th>Categoria Pai</th>
          <th>Cor</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody id="tabela-categorias">
        <tr>
          <td colspan="4" class="text-center">Carregando...</td>
        </tr>
      </tbody>
    </table>
  </div>
</main>

<div class="modal fade" id="modalCategoria" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-categoria">
        <div class="modal-header">
          <h5 class="modal-title">Categoria</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="id" name="id">

          <div class="mb-3">
            <label for="categoria" class="form-label">Nome</label>
            <input type="text" id="categoria" name="categoria" class="form-control" required>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="pai" name="pai">
            <label class="form-check-label" for="pai">Esta é uma categoria pai</label>
          </div>
          <div class="mb-3">
            <label for="tipo" class="form-label">Tipo da Categoria</label>
            <select class="form-select" id="tipo_lancamento" name="tipo" required>
              <option value="Receita">Receita</option>
              <option value="Despesa" selected>Despesa</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="categoria_pai" class="form-label">Categoria Pai</label>
            <select id="categoria_pai" name="categoria_pai" class="form-select">
              <option value="">Nenhuma</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="cor_texto" class="form-label">Cor do texto</label>
            <input type="color" id="cor_texto" name="cor_texto" class="form-control form-control-color" value="#000000">
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
<script src="js/categorias.js"></script>
