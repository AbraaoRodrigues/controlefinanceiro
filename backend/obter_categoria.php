<?php
header('Content-Type: application/json');

include '../includes/conexao.php';

$id = $_GET['id'] ?? null;
error_log('Chegou na função obter_categoria com ID: ' . ($id ?? 'null'));

if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido ou não fornecido']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, categoria, cor, categoria_pai_id FROM categorias WHERE id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($categoria) {
        echo json_encode($categoria);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Categoria não encontrada']);
    }
} catch (PDOException $e) {
    error_log('Erro ao buscar categoria: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno no servidor.']);
}
