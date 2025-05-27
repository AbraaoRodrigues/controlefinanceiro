<?php
session_start();
require_once '../includes/conexao.php';
header("Content-Type: application/json");

$uid = $_SESSION['usuario_id'] ?? 1;               // ajuste depois de login
$acao = $_POST['acao'] ?? '';

if ($acao === 'salvar') {
  $id     = $_POST['id'] ?? null;
  $nome   = trim($_POST['nome_conta'] ?? '');
  $banco  = trim($_POST['banco'] ?? '');
  $saldo = $_POST['saldo_inicial'] ?? '0';
  $saldo = str_replace(['R$', ' ', '.'], '', $saldo); // rem R$ e pontos
  $saldo = str_replace(',', '.', $saldo);             // vírgula → ponto
  $saldo = floatval($saldo);                          // agora número real

  if (!$nome || !$banco) {
    echo json_encode(["success" => false, "message" => "Campos obrigatórios"]);
    exit;
  }

  if ($id) {
    $sql = "UPDATE contas_bancarias
                SET nome_conta=?, banco=?, saldo_inicial=?
                WHERE id=? AND usuario_id=?";
    $pdo->prepare($sql)->execute([$nome, $banco, $saldo, $id, $uid]);
  } else {
    $sql = "INSERT INTO contas_bancarias
                (nome_conta,banco,saldo_inicial,usuario_id)
                VALUES (?,?,?,?)";
    $pdo->prepare($sql)->execute([$nome, $banco, $saldo, $uid]);
  }
  echo json_encode(["success" => true, "message" => "Conta salva!"]);
  exit;
}

if ($acao === 'excluir') {
  $id = $_POST['id'] ?? null;
  if (!$id) {
    echo json_encode(["success" => false]);
    exit;
  }

  // verifica vínculo (ex.: lançamentos)
  $vinc = $pdo->prepare("SELECT COUNT(*) FROM lancamentos WHERE conta_id=?");
  $vinc->execute([$id]);
  if ($vinc->fetchColumn() > 0) {
    $pdo->prepare("UPDATE contas_bancarias SET status='Arquivada'
                       WHERE id=? AND usuario_id=?")
      ->execute([$id, $uid]);
    echo json_encode(["success" => true, "message" => "Conta arquivada."]);
  } else {
    $pdo->prepare("DELETE FROM contas_bancarias
                       WHERE id=? AND usuario_id=?")
      ->execute([$id, $uid]);
    echo json_encode(["success" => true, "message" => "Conta excluída."]);
  }
  exit;
}

if ($acao === 'buscar') {
  $stmt = $pdo->prepare("SELECT * FROM contas_bancarias
                           WHERE usuario_id=? ORDER BY nome_conta");
  $stmt->execute([$uid]);
  echo json_encode(["success" => true, "dados" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
  exit;
}

echo json_encode(["success" => false, "message" => "Ação inválida"]);
