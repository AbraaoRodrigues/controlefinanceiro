<?php
include '../includes/conexao.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tipo = $_POST['tipo'] ?? null;
    $categoria_id = $_POST['categoria'] ?? null;
$valor = isset($_POST['valor']) ? str_replace(',', '.', $_POST['valor']) : null;

if (!is_numeric($valor)) {
    echo json_encode(["success" => false, "error" => "Valor inválido"]);
    exit;
}

    $descricao = $_POST['descricao'] ?? null;
    $data = $_POST['data'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$tipo || !$categoria_id || !$valor || !$data || !$status) {
        echo json_encode(["success" => false, "error" => "Todos os campos são obrigatórios!"]);
        exit;
    }

    try {
        $query = $pdo->prepare("INSERT INTO transacoes (tipo, categoria_id, valor, descricao, data, status) VALUES (?, ?, ?, ?, ?, ?)");
        $query->execute([$tipo, $categoria_id, $valor, $descricao, $data, $status]);

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método inválido"]);
}
?>
