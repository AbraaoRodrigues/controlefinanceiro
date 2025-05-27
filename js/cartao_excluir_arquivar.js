document.addEventListener('DOMContentLoaded', function () {
  const tabela = document.getElementById('tabela-cartoes');

  if (!tabela) return;

  tabela.addEventListener('click', function (e) {
    // Exclusão ou arquivamento
    if (e.target.classList.contains('btn-excluir-cartao')) {
      const id = e.target.dataset.id;
      if (!id) return;

      if (!confirm('Tem certeza que deseja excluir ou arquivar este cartão?')) return;

      fetch('backend/excluir_ou_arquivar_cartao.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${encodeURIComponent(id)}`
      })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.success) {
            location.reload();
          }
        })
        .catch(() => alert('Erro ao comunicar com o servidor.'));
    }

    // Ativar cartão arquivado
    if (e.target.classList.contains('btn-ativar-cartao')) {
      const id = e.target.dataset.id;
      if (!id) return;

      if (!confirm('Deseja reativar este cartão?')) return;

      fetch('backend/ativar_cartao.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${encodeURIComponent(id)}`
      })
        .then(res => res.json())
        .then(data => {
          alert(data.message);
          if (data.success) {
            location.reload();
          }
        })
        .catch(() => alert('Erro ao tentar ativar o cartão.'));
    }
  });
});
