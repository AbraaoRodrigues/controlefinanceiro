<?php
include '../includes/conexao.php'; // Certifique-se de que a conexão está correta

header('Content-Type: application/json');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Valida se o ID foi enviado
    if (!isset($input['id'])) {
        echo json_encode(['success' => false, 'error' => 'ID não informado.']);
        exit;
    }

    // Valida o ID
    $id = filter_var($input['id'], FILTER_VALIDATE_INT);
    if (!$id) {
        echo json_encode(['success' => false, 'error' => 'ID inválido.']);
        exit;
    }

    // Exclui o registro
    try {
        $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Registro não encontrado.']);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Erro no banco de dados.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Requisição inválida.']);
}
