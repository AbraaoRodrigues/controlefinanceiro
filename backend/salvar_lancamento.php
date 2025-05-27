<?php
session_start();
file_put_contents('debug_lanc.txt', print_r($_POST, true), FILE_APPEND);

require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Sessão expirada."]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];

$id          = $_POST['id'] ?? null;
$descricao   = trim($_POST['descricao'] ?? '');
$tipo = $_POST['tipo'] ?? ($_POST['tipo_lancamento'] ?? '');
$categoria_id = $_POST['categoria_id'] ?? null;
$data        = $_POST['data'] ?? date('Y-m-d');
$status      = $_POST['status'] ?? 'Pendente';

$valor = $_POST['valor'] ?? '0';
$valor = str_replace(['R$', ' ', '.'], '', $valor); // remove R$, espaços, pontos
$valor = str_replace(',', '.', $valor);             // vírgula -> ponto
$valor = floatval($valor);                          // 150,00 -> 150.00

if ($valor <= 0) {
  echo json_encode(["success" => false, "message" => "Valor deve ser maior que zero."]);
  exit;
}


if (!$descricao || !$tipo || !$valor || !$data || !$status) {
  echo json_encode(["success" => false, "message" => "Campos obrigatórios não preenchidos."]);
  exit;
}

try {
  if ($id) {
    // Atualizar lançamento existente
    $sql = "UPDATE lancamentos SET descricao = :descricao, tipo = :tipo, categoria_id = :categoria_id,
            valor = :valor, data = :data, status = :status
            WHERE id = :id AND usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':descricao' => $descricao,
      ':tipo' => $tipo,
      ':categoria_id' => $categoria_id ?: null,
      ':valor' => $valor,
      ':data' => $data,
      ':status' => $status,
      ':id' => $id,
      ':usuario_id' => $usuario_id
    ]);
  } else {
    // Novo lançamento
    $sql = "INSERT INTO lancamentos (descricao, tipo, categoria_id, valor, data, status, usuario_id)
            VALUES (:descricao, :tipo, :categoria_id, :valor, :data, :status, :usuario_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ':descricao' => $descricao,
      ':tipo' => $tipo,
      ':categoria_id' => $categoria_id ?: null,
      ':valor' => $valor,
      ':data' => $data,
      ':status' => $status,
      ':usuario_id' => $usuario_id
    ]);
  }

  echo json_encode(["success" => true, "message" => "Lançamento salvo com sucesso!"]);
} catch (Exception $e) {
  echo json_encode(["success" => false, "message" => "Erro: " . $e->getMessage()]);
}
