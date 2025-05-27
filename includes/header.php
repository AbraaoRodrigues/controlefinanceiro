<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}
?>
<!-- Menu superior -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Financeiro</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="index.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="lancamentos.php">Lançamentos</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="cartoesDropdown" role="button" aria-expanded="false">
            Cartões
          </a>
          <ul class="dropdown-menu" aria-labelledby="cartoesDropdown">
            <li><a class="dropdown-item" href="cartoes.php">Cartões</a></li>
            <li><a class="dropdown-item" href="registro_compra_cartao.php">Lançar Compra</a></li>
            <li><a class="dropdown-item" href="faturas_cartao.php">Faturas</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="contas.php">Contas Bancárias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="categorias.php">Categorias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-danger" href="logout.php">Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
