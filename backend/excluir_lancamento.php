<?php
header("Content-Type: application/json");
require_once "../includes/conexao.php"; // Conectar ao banco

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    echo json_encode(["error" => "ID não fornecido."]);
    exit;
}

$id = intval($data['id']);

$sql = "DELETE FROM transacoes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Erro ao excluir o lançamento."]);
}

$stmt->close();
$conn->close();
?>
