<?php 
include '../includes/header.php'; 
include 'verifica_login.php'; 
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Lançamento</title>
    <link rel="stylesheet" href="../css/cadastro_lancamentos.css"> <!-- CSS básico -->
    <link rel="stylesheet" href="../css/style.css"> <!-- CSS básico -->
   
    <script src="../js/mascara.js"></script>
    
</head>
<body>

    <h2 style="text-align: center; margin-top: 20px;">Cadastro de Lançamento</h2>

    <form id="form-lancamento">
    <div class="form-group">
        <label for="data">Data do Pagamento:</label>
        <input type="date" id="data" name="data" required>
    </div>

    <div class="form-group">
        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="">Selecione</option>
            <option value="Receita">Receita</option>
            <option value="Despesa">Despesa</option>
        </select>
    </div>

    <div class="form-group">
        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria" required>
    <option value="">Carregando...</option>
</select>

        </select>
    </div>

    <div class="form-group">
        <label for="valor">Valor:</label>
        <input type="text" id="valor" name="valor" class="campo-valor" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" required>
    </div>

    <div class="form-group">
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="">Selecione</option>
            <option value="Efetivada">Efetivada</option>
            <option value="Pendente">Pendente</option>
        </select>
    </div>

    <button type="submit">Salvar Lançamento</button></br>
    <button type="button" id="voltarPagina">Voltar</button>

</form>
<!-- Modal ou Mensagem de Sucesso -->
<div id="msg-sucesso" style="display: none; color: green; margin-top: 10px;">
    Lançamento cadastrado com sucesso!
</div>
<div id="msg-erro" style="display: none; color: red; margin-top: 10px;">
    Ocorreu um erro ao cadastrar o lançamento.
</div>
<script src="https://unpkg.com/imask.js" defer></script>
 <script src="../js/cadastro_lancamento.js"></script>
</body>
</html>
<?php include '../includes/footer.php'; ?>