document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('form-lancamento');
  if (!form) return;

  form.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData(form);

    fetch('backend/salvar_lancamento.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          form.reset();
          // TODO: atualizar tabela de lançamentos dinamicamente
          const modalEl = document.getElementById('modalNovo');
          const modalInstance = bootstrap.Modal.getInstance(modalEl);
          if (modalInstance) modalInstance.hide();
          // garante remoção total da backdrop
setTimeout(() => {
  document.querySelectorAll('.modal-backdrop')
          .forEach(bd => bd.remove());
  document.body.classList.remove('modal-open');
}, 300);   // tempo igual à animação (300 ms)

          // Recarregar lançamentos
          if (typeof carregarLancamentos === 'function') carregarLancamentos();
        } else {
          alert(data.message || 'Erro ao salvar lançamento.');
        }
      })
      .catch(() => {
        alert('Erro na comunicação com o servidor.');
      });
  });
});

document.getElementById('btnNovoLancamento').addEventListener('click', () => {
  const form = document.getElementById('form-lancamento');
  form.reset();                              // limpa todos os campos
  document.getElementById('id').value = '';  // limpa campo oculto
});
