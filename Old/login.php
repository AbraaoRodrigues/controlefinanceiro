<?php
$erro = isset($_GET['erro']) ? intval($_GET['erro']) : 0;
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <form id="formLogin">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha:</label>
                <input type="password" id="senha" name="senha" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>
    </div>

    <!-- Modal de Erro -->
    <div class="modal fade" id="modalErro" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Erro no Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Email ou senha incorretos. Tente novamente.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
       $('#formLogin').on('submit', function (e) {
    e.preventDefault(); // Impede o envio tradicional do formulário

    const email = $('#email').val().trim();
    const senha = $('#senha').val().trim();

    $.ajax({
        url: '../backend/processa_login.php',
        method: 'POST',
        data: { email, senha },
        success: function (response) {
            console.log('Resposta do servidor:', response); // Exibe o retorno do PHP
            if (response.trim() === 'success') {
                window.location.href = '../index.php'; // Redireciona em caso de sucesso
            } else {
                $('#modalErro').modal('show'); // Exibe o modal em caso de erro
            }
        },
        error: function () {
            alert('Erro na comunicação com o servidor.');
        }
    });
});

    </script>
    
    <script>
    const erro = <?php echo $erro; ?>;
    if (erro === 1) {
        alert('Email ou senha incorretos.');
        // Opcional: foco no primeiro campo para o usuário corrigir
        document.getElementById('email').focus();
    }
</script>

    
</body>
</html>
