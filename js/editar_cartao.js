document.addEventListener('DOMContentLoaded', function () {
  const tabela = document.getElementById('tabela-cartoes');

  tabela.addEventListener('click', function (event) {
    const botao = event.target.closest('.btn-editar-cartao');
    if (!botao) return;

    const id = botao.dataset.id;

    fetch(`backend/obter_cartao.php?id=${id}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const c = data.cartao;
          document.getElementById('form-cartao').reset();
          document.getElementById('cartao_id').value = c.id;
          document.getElementById('nome_cartao').value = c.nome_cartao;
          document.getElementById('bandeira').value = c.bandeira;
          document.getElementById('limite').value = 'R$ ' + parseFloat(c.limite).toFixed(2).replace('.', ',');
          document.getElementById('fechamento').value = c.fechamento;
          document.getElementById('vencimento').value = c.vencimento;
          document.getElementById('status').value = c.status;

          const modal = new bootstrap.Modal(document.getElementById('modalCartao'));
          modal.show();
        } else {
          alert('Erro: ' + data.message);
        }
      })
      .catch(() => alert('Erro ao buscar cartão.'));
  });
});
