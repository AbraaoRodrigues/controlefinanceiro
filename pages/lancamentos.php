<?php include '../includes/header.php'; 
include 'verifica_login.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançamentos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/lancamentos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="../js/mascara.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Lançamentos Financeiros</h2>
        <div class="d-flex">
            <!-- Painel de Filtros -->
            <div id="filterPanel" class="filter-panel">
                <div id="filterHeader" class="mb-3">
                    <button id="btnMinimizarFiltros">Minimizar</button>
                </div>
                <div id="filtrosContainer">
                <h5>Filtros</h5>
                <form id="filterForm">
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria:</label>
                        <select id="categoria" name="categoria" class="form-select">
                            <option value="">Todas</option>
                            <option value="Receitas">Receitas</option>
                            <option value="Despesas">Despesas</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dataInicio" class="form-label">De:</label>
                        <input type="date" id="dataInicio" name="dataInicio" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="dataFim" class="form-label">Até:</label>
                        <input type="date" id="dataFim" name="dataFim" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="valorMin" class="form-label">Valor Mínimo:</label>
                        <input type="text" id="valorMin" class="campo-valor" placeholder="Valor mínimo">
                    </div>
                    <div class="mb-3">
                        <label for="valorMax" class="form-label">Valor Máximo:</label>
                        <input type="text" id="valorMax" class="campo-valor" placeholder="Valor máximo">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="Pendente">Pendente</option>
                            <option value="Efetivada">Efetivada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    <button type="button" id="limparFiltros">Limpar Filtros</button>
                </form>
            </div>
            </div>

            <!-- Tabela -->
            <div class="flex-grow-1 ms-3">
                <a id="abrirPaginaLancamento" href="cadastro_lancamentos.php" class="btn btn-primary">Adicionar Lançamento</a>
                <table class="table table-striped">
    <thead>
        <tr>
            <th>Data</th>
            <th>Categoria</th>
            <th>Descrição</th>
            <th>Valor</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody id="lancamentosTable">
        <!-- Os dados serão inseridos aqui dinamicamente -->
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><strong>Total</strong></td>
            <td id="totalValor"><strong>R$ 0,00</strong></td>
            <td></td>
        </tr>
    </tfoot>
</table>
            </div>
        </div>
    </div>
    <script src="../js/lancamentos.js"></script>

</body>
</html>

<?php include '../includes/footer.php'; ?>
