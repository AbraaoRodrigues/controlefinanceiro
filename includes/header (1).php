<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<header class="bg-dark text-white py-2 shadow">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h4 mb-0">Controle Financeiro</h1>
        <div>
            <span>Olá, <?= htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário') ?></span>
            <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Sair</a>
        </div>
    </div>
</header>
