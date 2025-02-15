<?php
include '../includes/header.php';
include '../includes/conexao.php';
include 'includes/verifica_login.php';

session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Função recursiva para exibir categorias com hierarquia
function exibirCategorias($pdo, $categoriaPaiId = null, $nivel = 0) {
    $sql = "SELECT id, categoria, cor FROM categorias WHERE categoria_pai_id " . 
           ($categoriaPaiId ? "= :categoriaPaiId" : "IS NULL") . 
           " ORDER BY categoria ASC";
    $stmt = $pdo->prepare($sql);

    if ($categoriaPaiId) {
        $stmt->bindParam(':categoriaPaiId', $categoriaPaiId, PDO::PARAM_INT);
    }

    $stmt->execute();
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($categorias as $categoria) {
        $recuo = str_repeat('&nbsp;&nbsp;&nbsp;', $nivel);
        $corTexto = htmlspecialchars($categoria['cor'] ?? '#000000'); // Cor padrão
        $estiloNegrito = $nivel === 0 ? 'font-weight: bold;' : ''; // Negrito para categorias pai

        echo "<tr>
                <td style='color: {$corTexto}; {$estiloNegrito}'>{$recuo}" . htmlspecialchars($categoria['categoria']) . "</td>
                <td style='text-align: center;'>" . ($nivel > 0 ? 'Filha' : 'Pai') . "</td>
                <td class='actions'>
                    <i class='fas fa-pencil-alt editar-categoria' data-id='" . htmlspecialchars($categoria['id']) . "' title='Editar'></i>
                    <i class='fas fa-trash-alt excluir-categoria' data-id='" . htmlspecialchars($categoria['id']) . "' title='Excluir' onclick='excluirCategoria(" . htmlspecialchars($categoria['id']) . ")'></i>
                </td>
              </tr>";

        // Exibe categorias filhas
        exibirCategorias($pdo, $categoria['id'], $nivel + 1);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Categorias</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/categorias.css">
</head>
<body>
    <div class="container">
        <h1>Cadastro de Categorias</h1>
        <form method="POST" action="../backend/categorias.php" id="form-categoria">
            <!-- Campo oculto para armazenar o ID da categoria -->
            <input type="hidden" id="categoria_id" name="id">

            <label for="categoria">Nome da Categoria:</label>
            <input type="text" id="categoria" name="categoria" required>

            <div class="form-group">
                <label for="cor">Cor:</label>
                <input type="color" id="cor" name="cor" value="#000000" onchange="atualizarHexadecimal(this)">
                <input type="text" id="hex_cor" value="#000000" readonly>
            </div>

            <script>
                function atualizarHexadecimal(input) {
                    document.getElementById('hex_cor').value = input.value;
                }
            </script>

            <label>
                <input type="checkbox" id="categoria_pai_check" name="categoria_pai_check">
                Categoria Pai
            </label>

            <label for="categoria_pai">Categoria Pai:</label>
            <select id="categoria_pai" name="categoria_pai">
                <option value="">Nenhuma</option>
                <?php
                $sql = "SELECT id, categoria FROM categorias WHERE categoria_pai_id IS NULL";
                $stmt = $pdo->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['categoria']) . "</option>";
                }
                ?>
            </select>

            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <button type="submit" id="salvar-categoria">Salvar</button>
        </form>
    </div>
    
    <section>
        <h3>Categorias Existentes</h3>
        <table>
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Tipo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php exibirCategorias($pdo); ?>
            </tbody>
        </table>
    </section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Previne o comportamento padrão do formulário e salva via AJAX
    document.getElementById('salvar-categoria').addEventListener('click', function (e) {
        e.preventDefault(); // Previne o redirecionamento

        const formData = new FormData(document.getElementById('form-categoria'));

        fetch('../backend/categorias.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message); // Exibe a mensagem de sucesso
                    location.reload(); // Atualiza a página para refletir as mudanças
                } else {
                    alert('Erro: ' + data.message); // Exibe a mensagem de erro, se houver
                }
            })
            .catch(error => {
                console.error('Erro ao salvar categoria:', error);
                alert('Erro na comunicação com o servidor.');
            });
    });

    // Editar categoria (já funcionando corretamente)
    document.querySelectorAll('.editar-categoria').forEach(button => {
        button.addEventListener('click', function () {
            const categoriaId = this.dataset.id;

            fetch(`../backend/obter_categoria.php?id=${categoriaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) throw new Error(data.error);

                    document.getElementById('categoria').value = data.categoria || '';
                    document.getElementById('cor').value = data.cor || '#000000';
                    document.getElementById('categoria_pai').value = data.categoria_pai_id || '';
                    document.getElementById('categoria_id').value = data.id || '';
                })
                .catch(error => {
                    alert(error.message || 'Erro ao carregar categoria.');
                });
        });
    });
    
    function configurarBotoesEdicao() {
        const botoesEditar = document.querySelectorAll('.editar-categoria');

        botoesEditar.forEach(botao => {
            botao.addEventListener('click', function () {
                const categoriaId = this.dataset.id;

                fetch(`../backend/obter_categoria.php?id=${categoriaId}`)
    .then(response => {
        if (!response.ok) throw new Error('Erro ao buscar categoria');
        return response.json();
    })
    .then(data => {
        if (data.error) throw new Error(data.error);

        // Preenche o formulário com os dados da categoria
        document.getElementById('categoria_id').value = data.id || ''; // Define o ID
        document.getElementById('categoria').value = data.categoria || '';
        document.getElementById('cor').value = data.cor || '#000000';
        document.getElementById('hex_cor').value = data.cor || '#000000';
        document.getElementById('categoria_pai').value = data.categoria_pai_id || '';
    })
    .catch(error => {
        alert(error.message || 'Erro ao carregar categoria.');
    });

            });
        });
    }

    // Reconfigurar botões sempre que houver mudanças dinâmicas na tabela
    const categoriasTbody = document.querySelector('#categorias-tbody');
    if (categoriasTbody) {
        const observer = new MutationObserver(() => configurarBotoesEdicao());
        observer.observe(categoriasTbody, { childList: true });
    }
    });
     // Função para excluir categoria
    function excluirCategoria(id) {
        if (!confirm('Tem certeza que deseja excluir esta categoria?')) return;

        fetch('../backend/excluir.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Categoria excluída com sucesso!');
                location.reload();
            } else {
                alert(`Erro: ${data.error}`);
            }
        })
        .catch(error => {
            alert('Erro na comunicação com o servidor.');
        });
    }

</script>
</body>
</html>

<?php include '../includes/footer.php'; ?>
