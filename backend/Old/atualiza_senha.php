<?php
require_once '../includes/conexao.php'; // Conexão com o banco de dados

// Dados do usuário
$email = 'renataplacidelli@gmail.com'; // Email do usuário que será atualizado
$senha_plana = 'Superpla300890';    // Nova senha para o usuário

// Gera o hash da senha
$senha_hash = password_hash($senha_plana, PASSWORD_DEFAULT);

// Atualiza no banco de dados
$stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = :email");
$stmt->execute(['senha' => $senha_hash, 'email' => $email]);

if ($stmt->rowCount() > 0) {
    echo "Senha atualizada com sucesso!";
} else {
    echo "Erro ao atualizar a senha ou usuário não encontrado.";
}
?>
