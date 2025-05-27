document.addEventListener('DOMContentLoaded', () => {
  const tbody = document.getElementById('tabela-cartoes');
  if (!tbody) return;

  /* ---- Carregar cartões ---- */
  fetch('backend/buscar_cartoes.php')
    .then(r => r.json())
    .then(r => {
      if (!r.success || !Array.isArray(r.dados)) {
        alert('Erro ao carregar cartões.');
        return;
      }
      tbody.innerHTML = '';
      r.dados.forEach(c => {
        const tr = document.createElement('tr');
        const acoes = c.status === 'Arquivado'
          ? `<button class="btn btn-sm btn-outline-warning btn-ativar-cartao" data-id="${c.id}">Ativar</button>`
          : `<button class="btn btn-sm btn-outline-primary btn-editar-cartao" data-id="${c.id}">Editar</button>
             <button class="btn btn-sm btn-outline-danger btn-excluir-cartao" data-id="${c.id}">Excluir</button>`;

        tr.innerHTML = `
          <td>${c.nome_cartao}</td>
          <td>${c.bandeira ?? '-'}</td>
          <td>R$ ${parseFloat(c.limite).toFixed(2).replace('.', ',')}</td>
          <td>${c.fechamento}</td>
          <td>${c.vencimento}</td>
          <td><span class="badge ${c.status === 'Ativo' ? 'bg-success' : 'bg-secondary'}">${c.status}</span></td>
          <td>${acoes}</td>`;
        tbody.appendChild(tr);
      });
    });

  /* ---- Delegação de eventos sem cliques duplos ---- */
  tbody.addEventListener('click', e => {
    /* Excluir / Arquivar */
    const btnExcluir = e.target.closest('.btn-excluir-cartao');
    if (btnExcluir) {
      const id = btnExcluir.dataset.id;
      if (!id) return;
      if (!confirm('Tem certeza que deseja excluir ou arquivar este cartão?')) return;

      fetch('backend/excluir_ou_arquivar_cartao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}`
      })
        .then(r => r.json())
        .then(d => { alert(d.message); if (d.success) location.reload(); })
        .catch(() => alert('Erro ao comunicar com o servidor.'));
      return; // garante que não continue para outro botão
    }

    /* Ativar */
    const btnAtivar = e.target.closest('.btn-ativar-cartao');
    if (btnAtivar) {
      const id = btnAtivar.dataset.id;
      if (!id) return;
      if (!confirm('Deseja reativar este cartão?')) return;

      fetch('backend/ativar_cartao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${encodeURIComponent(id)}`
      })
        .then(r => r.json())
        .then(d => { alert(d.message); if (d.success) location.reload(); })
        .catch(() => alert('Erro ao tentar ativar o cartão.'));
    }
  });
});
