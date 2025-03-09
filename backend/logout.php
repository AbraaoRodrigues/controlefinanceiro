<?php
session_start();      // Garante que a sessão está ativa
session_destroy();    // Destroi a sessão do usuário
header("Location: ../login.php"); // Ou outra página
exit;
