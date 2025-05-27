<?php
header('Content-Type: application/json');
include '../includes/conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID do lançamento não informado.']);
    exit;
}

try {
    $sql = "UPDATE transacoes SET 
                data = :data, 
                categoria_id = :categoria, 
                descricao = :descricao, 
                valor = :valor, 
                status = :status 
            WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':data', $data['data']);
    $stmt->bindParam(':categoria', $data['categoria']);
    $stmt->bindParam(':descricao', $data['descricao']);
    $stmt->bindParam(':valor', $data['valor']);
    $stmt->bindParam(':status', $data['status']);
    $stmt->bindParam(':id', $data['id']);
    
    $stmt->execute();

    echo json_encode(['sucesso' => true]);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
?>
