<?php
session_start();
require_once '../includes/conexao.php';

header("Content-Type: application/json");

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(["success" => false, "message" => "Usuário não autenticado."]);
  exit;
}

$usuario_id = $_SESSION['usuario_id'];

$acao = $_POST['acao'] ?? '';

if ($acao === 'salvar') {
  $id            = $_POST['id'] ?? null;
  $categoria     = trim($_POST['categoria'] ?? '');
  $categoria_pai = $_POST['categoria_pai'] ?? null;
  $tipo = $_POST['tipo'] ?? 'Despesa';  // Padrão como Despesa
  $cor_texto     = $_POST['cor_texto'] ?? '#000000';

  if (!$categoria) {
    echo json_encode(["success" => false, "message" => "Nome da categoria é obrigatório."]);
    exit;
  }

  $pai = (isset($_POST['pai']) && $_POST['pai'] == '1') ? 1 : 0;

  if ($id) {
    $stmt = $pdo->prepare("UPDATE categorias SET categoria = ?, categoria_pai_id = ?, cor_texto = ?, pai = ?, tipo = ? WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$categoria, $categoria_pai ?: null, $cor_texto, $pai, $tipo, $id, $usuario_id]);
  } else {
    $stmt = $pdo->prepare("INSERT INTO categorias (categoria, categoria_pai_id, cor_texto, pai, tipo, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$categoria, $categoria_pai ?: null, $cor_texto, $pai, $tipo, $usuario_id]);
  }

  echo json_encode(["success" => true, "message" => "Categoria salva com sucesso!"]);
  exit;
}

if ($acao === 'excluir') {
  $id = $_POST['id'] ?? null;

  if (!$id) {
    echo json_encode(["success" => false, "message" => "ID da categoria não informado."]);
    exit;
  }

  // Verifica se a categoria tem filhos ou está sendo usada
  $hasFilhos = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE categoria_pai_id = ? AND usuario_id = ?");
  $hasFilhos->execute([$id, $usuario_id]);
  if ($hasFilhos->fetchColumn() > 0) {
    echo json_encode(["success" => false, "message" => "Esta categoria possui subcategorias vinculadas."]);
    exit;
  }

  $stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ? AND usuario_id = ?");
  $stmt->execute([$id, $usuario_id]);

  echo json_encode(["success" => true, "message" => "Categoria excluída com sucesso!"]);
  exit;
}

if ($acao === 'buscar') {
  $stmt = $pdo->prepare("SELECT * FROM categorias WHERE usuario_id = ? ORDER BY categoria");
  $stmt->execute([$usuario_id]);
  $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(["success" => true, "dados" => $categorias]);
  exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida."]);
