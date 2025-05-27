<?php
session_start();
require_once '../includes/conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
  echo json_encode(['success' => false, 'message' => 'Sessão expirada.']);
  exit;
}

// Receber dados do POST
$cartao_id    = $_POST['cartao_id'] ?? null;
$nome_cartao  = $_POST['nome_cartao'] ?? '';
$bandeira     = $_POST['bandeira'] ?? '';
$limite       = str_replace([',', 'R$', ' '], ['.', '', ''], $_POST['limite'] ?? '0');
$fechamento   = $_POST['fechamento'] ?? null;
$vencimento   = $_POST['vencimento'] ?? null;
$status       = $_POST['status'] ?? 'Ativo';
$usuario_id   = $_SESSION['usuario_id'];

if (!$nome_cartao || !$limite || !$fechamento || !$vencimento) {
  echo json_encode(['success' => false, 'message' => 'Todos os campos obrigatórios devem ser preenchidos.']);
  exit;
}

try {
  if ($cartao_id) {
    // Atualizar
    $stmt = $pdo->prepare("UPDATE cartoes SET
            nome_cartao = :nome,
            bandeira = :bandeira,
            limite = :limite,
            fechamento = :fechamento,
            vencimento = :vencimento,
            status = :status
            WHERE id = :id AND usuario_id = :usuario_id");

    $stmt->execute([
      ':nome' => $nome_cartao,
      ':bandeira' => $bandeira,
      ':limite' => $limite,
      ':fechamento' => $fechamento,
      ':vencimento' => $vencimento,
      ':status' => $status,
      ':id' => $cartao_id,
      ':usuario_id' => $usuario_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Cartão atualizado com sucesso!']);
  } else {
    // Inserir novo
    $stmt = $pdo->prepare("INSERT INTO cartoes
            (nome_cartao, bandeira, limite, fechamento, vencimento, status, usuario_id)
            VALUES
            (:nome, :bandeira, :limite, :fechamento, :vencimento, :status, :usuario_id)");

    $stmt->execute([
      ':nome' => $nome_cartao,
      ':bandeira' => $bandeira,
      ':limite' => $limite,
      ':fechamento' => $fechamento,
      ':vencimento' => $vencimento,
      ':status' => $status,
      ':usuario_id' => $usuario_id
    ]);

    echo json_encode(['success' => true, 'message' => 'Cartão cadastrado com sucesso!']);
  }
} catch (Exception $e) {
  echo json_encode(['success' => false, 'message' => 'Erro ao salvar: ' . $e->getMessage()]);
}
