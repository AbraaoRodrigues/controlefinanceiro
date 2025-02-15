<?php
$senha = '1a2b3c45';
$hash_armazenado = '$2y$10$qDLSuZB2oo9Xhbz/D4YQE.sF1n5ABkme4HzhfrerOxX7f/2jK2Jd.';
if (password_verify($senha, $hash_armazenado)) {
    echo "Senha válida!";
} else {
    echo "Senha incorreta.";
}
?>
