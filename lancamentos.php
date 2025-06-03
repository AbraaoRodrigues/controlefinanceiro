<?php
session_start();
//require_once 'includes/verifica_login.php';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lançamentos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_base.css">
</head>

<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>

  <main class="container my-4 flex-grow-1">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0">Lançamentos</h2>
      <button
        id="btnNovoLancamento"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalNovo">
        <i class="fas fa-plus"></i> Novo Lançamento
      </button>
    </div>

    <!-- Filtros avançados -->
    <div class="card mb-4 shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Filtros</strong>
        <button class="btn btn-sm btn-outline-secondary" type="button"
          data-bs-toggle="collapse" data-bs-target="#filtrosAvancados"
          aria-expanded="true" aria-controls="filtrosAvancados">
          Minimizar
        </button>
      </div>
      <div class="collapse show" id="filtrosAvancados">
        <form id="form-filtros-lancamentos" class="row g-3 p-3">
          <div class="col-md-3">
            <label for="filtro-inicio" class="form-label">Data Início</label>
            <input type="date" class="form-control" id="filtro-inicio" name="inicio">
          </div>
          <div class="col-md-3">
            <label for="filtro-fim" class="form-label">Data Fim</label>
            <input type="date" class="form-control" id="filtro-fim" name="fim">
          </div>
          <div class="col-md-3">
            <label for="filtro-categoria" class="form-label">Categoria</label>
            <select id="filtro-categoria" name="categoria" class="form-select">
              <option value="">Todas</option>
              <option value="1">Alimentação</option>
              <option value="2">Moradia</option>
              <option value="3">Transporte</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="filtro-status" class="form-label">Status</label>
            <select id="filtro-status" name="status" class="form-select">
              <option value="">Todos</option>
              <option>Pendente</option>
              <option>Pago</option>
            </select>
          </div>
          <div class="col-md-3">
            <label for="valor_min" class="form-label">Valor Mínimo</label>
            <input type="number" class="form-control" id="valor_min" name="valor_min">
          </div>
          <div class="col-md-3">
            <label for="valor_max" class="form-label">Valor Máximo</label>
            <input type="number" class="form-control" id="valor_max" name="valor_max">
          </div>
          <div class="col-md-3 align-self-end">
            <button class="btn btn-secondary w-100" type="submit">Filtrar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabela de lançamentos -->
    <div class="table-responsive">
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>Descrição</th>
            <th>Tipo</th>
            <th>Categoria</th>
            <th>Valor</th>
            <th>Data</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody id="tabela-lancamentos">
          <!-- Conteúdo dinâmico -->
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal de novo lançamento -->
  <div class="modal fade" id="modalNovo" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="form-lancamento">
          <input type="hidden" name="id" id="id">
          <div class="modal-header">
            <h5 class="modal-title">Novo Lançamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <input type="text" class="form-control" id="descricao" name="descricao" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tipo</label>
              <select class="form-select" id="tipo_lancamento" name="tipo" required>
                <option>Receita</option>
                <option>Despesa</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Categoria</label>
              <select class="form-select" id="categoria_id" name="categoria_id" required>
                <option value="">Selecione...</option>
                <!-- categorias serão carregadas via JS -->
              </select>
            </div>
            <label class="form-label">Conta</label>
            <select class="form-select" id="conta_id" name="conta_id" required>
              <option value="">Selecione</option>
              <!-- Carregado via JS -->
            </select>
            <div class="mb-3">
              <label class="form-label">Valor</label>
              <input type="text" class="form-control" id="valor" name="valor" placeholder="R$ 0,00">
            </div>
            <div class="mb-3">
              <label class="form-label">Data</label>
              <input type="date" class="form-control" id="data" name="data" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" id="status" name="status" required>
                <option>Pendente</option>
                <option>Pago</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" type="submit">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include 'includes/footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script src="js/filtros_lancamentos.js"></script>
  <script src="js/carregar_lancamentos.js"></script>
  <script src="js/salvar_lancamentos.js"></script>
  <script src="js/acoes_lancamentos.js"></script>

</body>

</html>
