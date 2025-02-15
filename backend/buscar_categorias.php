<?php
header('Content-Type: application/json');
include '../includes/conexao.php';

function buscarCategorias($pdo, $categoriaPaiId = null, $nivel = 0) {
    $sql = "SELECT id, categoria FROM categorias WHERE categoria_pai_id " . 
           ($categoriaPaiId ? "= :categoriaPaiId" : "IS NULL") . 
           " ORDER BY categoria ASC";
    $stmt = $pdo->prepare($sql);

    if ($categoriaPaiId) {
        $stmt->bindParam(':categoriaPaiId', $categoriaPaiId, PDO::PARAM_INT);
    }

    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $resultado = [];
    foreach ($categorias as $categoria) {
        $subcategorias = buscarCategorias($pdo, $categoria['id'], $nivel + 1);

        $resultado[] = [
            'id' => $categoria['id'],
            'categoria' => $categoria['categoria'],
            'nivel' => $nivel, // 0 = Pai, 1 = Filha, 2 = Neto...
            'subcategorias' => $subcategorias
        ];
    }

    return $resultado;
}

try {
    $categorias = buscarCategorias($pdo);
    echo json_encode($categorias);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar categorias: ' . $e->getMessage()]);
}
?>
