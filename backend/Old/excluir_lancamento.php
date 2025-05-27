<?php
<<<<<<< HEAD
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Sessão expirada."]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id = $_POST['id'] ?? null;

if (!$id) {
  echo json_encode(["success" => false, "message" => "ID do lançamento não informado."]);
  exit;
}

try {
  $stmt = $pdo->prepare("DELETE FROM lancamentos WHERE id = :id AND usuario_id = :usuario_id");
  $stmt->execute([':id' => $id, ':usuario_id' => $usuario_id]);

  if ($stmt->rowCount()) {
    echo json_encode(["success" => true, "message" => "Lançamento excluído com sucesso!"]);
  } else {
    echo json_encode(["success" => false, "message" => "Lançamento não encontrado ou não pertence ao usuário."]);
  }
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => "Erro ao excluir: " . $e->getMessage()]);
}
=======
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
>>>>>>> 0e975c84fd4b26b8c215b07fa060018ab3c4ba9a
