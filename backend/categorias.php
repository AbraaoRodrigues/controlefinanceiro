<?php
header('Content-Type: application/json');
include '../includes/conexao.php';

session_start();

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica o CSRF Token
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? null)) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'Token CSRF inválido ou ausente.']);
        exit;
    }

    try {
        // Recebe os dados do formulário
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_STRING);
        $cor = $_POST['cor'] ?? '#000000';
        $categoriaPaiCheck = isset($_POST['categoria_pai_check']);
        $categoriaPai = $categoriaPaiCheck ? null : (is_numeric($_POST['categoria_pai']) ? (int)$_POST['categoria_pai'] : null);
        $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;

        // Validações básicas
        if (empty($categoria)) {
            throw new Exception("O nome da categoria é obrigatório.");
        }

        if ($id) {
            // Atualização de categoria existente
            $sql = "SELECT categoria, cor, categoria_pai_id FROM categorias WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $categoriaExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$categoriaExistente) {
                throw new Exception("Categoria não encontrada para atualização.");
            }

            // Verifica se há alterações
            if (
                $categoria === $categoriaExistente['categoria'] &&
                $cor === $categoriaExistente['cor'] &&
                $categoriaPai == $categoriaExistente['categoria_pai_id']
            ) {
                echo json_encode(['success' => false, 'message' => 'Nenhuma alteração detectada.']);
                exit;
            }

            // Atualiza os dados
            $sql = "UPDATE categorias 
                    SET categoria = :categoria, cor = :cor, categoria_pai_id = :categoria_pai_id 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
            $stmt->bindParam(':cor', $cor, PDO::PARAM_STR);
            if ($categoriaPai === null) {
                $stmt->bindValue(':categoria_pai_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':categoria_pai_id', $categoriaPai, PDO::PARAM_INT);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Categoria atualizada com sucesso.']);
            exit;
        } else {
            // Inserção de nova categoria
            $sql = "INSERT INTO categorias (categoria, cor, categoria_pai_id) VALUES (:categoria, :cor, :categoria_pai_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
            $stmt->bindParam(':cor', $cor, PDO::PARAM_STR);
            if ($categoriaPai === null) {
                $stmt->bindValue(':categoria_pai_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindParam(':categoria_pai_id', $categoriaPai, PDO::PARAM_INT);
            }
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Nova categoria cadastrada com sucesso.']);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
} else {
    http_response_code(405); // Método não permitido
    echo json_encode(['success' => false, 'error' => 'Método inválido.']);
    exit;
}
