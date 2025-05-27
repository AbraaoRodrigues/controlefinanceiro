<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financeiro Familiar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/estilo_base.css">
</head>

<body class="d-flex flex-column min-vh-100">
  <?php include 'includes/header.php'; ?>
  <!-- Conteúdo -->
  <main class="container my-4 flex-grow-1">
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
  </main>

  <!-- Rodapé -->
  <footer class="bg-light text-center text-muted py-3 mt-auto">
    <?php include 'includes/footer.php'; ?>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
