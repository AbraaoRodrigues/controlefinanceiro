<?php

try {
    // Conexão ao banco com charset UTF-8
    $pdo = new PDO("mysql:host=localhost;dbname=u296399424_control_finan;charset=utf8", "u296399424_abraao", "*1A2b3c45*");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log do erro
    error_log("Erro ao conectar ao banco de dados: " . $e->getMessage());
            // Evita exibir informações sensíveis em páginas HTML
        die("Erro ao conectar ao banco de dados. Por favor, tente novamente mais tarde.");
    }

?>
