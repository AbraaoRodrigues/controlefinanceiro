// Quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function () {
  const select = document.getElementById('filtro_cartao');
  const lista = document.getElementById('lista-compras');
  const resumo = document.getElementById('resumo-fatura');
  const total = document.getElementById('total-fatura');
  const pendente = document.getElementById('valor-pendente');

  // Se algum dos elementos obrigatórios não estiver presente, sai do script
  if (!select || !lista || !resumo || !total || !pendente) return;

  // Oculta o resumo ao iniciar
  resumo.style.display = 'none';
  total.textContent = 'R$ 0,00';
  pendente.textContent = 'R$ 0,00';

  // Quando o usuário seleciona um cartão
  select.addEventListener('change', function () {
    const id = this.value;
    const mes = document.getElementById('filtro_mes').value;

    lista.innerHTML = '';
    resumo.style.setProperty('display', 'none');
    if (!id) return;

    // Requisição para buscar fatura e parcelas do cartão para o mês selecionado
    fetch(`backend/get_fatura_cartao.php?cartao_id=${id}&mes=${mes}`)
      .then(res => res.json())
      .then(data => {
        if (!data.fatura) {
          lista.innerHTML = '<div class="alert alert-info">Nenhuma fatura encontrada para este cartão no mês selecionado.</div>';
          return;
        }

        const fatura = data.fatura;
        const parcelas = data.parcelas;

        // Exibe valores no resumo
        resumo.style.display = 'flex';
        total.textContent = parseFloat(fatura.valor_total).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        pendente.textContent = fatura.status === 'Paga' ? 'R$ 0,00' : total.textContent;

        // Agrupa parcelas por compra
        let html = '';
        let comprasMap = new Map();

        parcelas.forEach(p => {
          const key = p.descricao + p.valor_total + p.data_compra;
          if (!comprasMap.has(key)) {
            comprasMap.set(key, []);
          }
          comprasMap.get(key).push(p);
        });

        // Monta HTML das parcelas
        comprasMap.forEach((parcelas, key) => {
          const p0 = parcelas[0];
          html += `
            <div class="card mb-3">
              <div class="card-header bg-primary text-white">
                <strong>${p0.descricao}</strong> - Valor Total: R$ ${parseFloat(p0.valor_total).toFixed(2).replace('.', ',')} (${p0.parcelas}x)
              </div>
              <div class="card-body p-0">
                <table class="table table-sm m-0">
                  <thead>
                    <tr>
                      <th>Parcela</th>
                      <th>Valor</th>
                      <th>Data Compra</th>
                    </tr>
                  </thead>
                  <tbody>
          `;
          parcelas.forEach((parcela, idx) => {
            html += `
              <tr>
                <td>${idx + 1}/${parcela.parcelas}</td>
                <td>R$ ${parseFloat(parcela.valor_parcela).toFixed(2).replace('.', ',')}</td>
                <td>${new Date(parcela.data_compra).toLocaleDateString('pt-BR')}</td>
              </tr>
            `;
          });
          html += '</tbody></table></div></div>';
        });

        // Exibe botão "Marcar como Paga" se a fatura estiver aberta
        if (fatura.status === 'Aberta') {
          html += `<button class="btn btn-success mt-2" onclick="marcarComoPaga(${fatura.id})">Marcar como Paga</button>`;
        }

        // Insere na página
        lista.innerHTML = html;
      })
      .catch(() => {
        lista.innerHTML = '<div class="alert alert-danger">Erro ao carregar a fatura.</div>';
      });
  });

  // Quando muda o mês, recarrega o cartão
  document.getElementById('filtro_mes').addEventListener('change', function () {
    select.dispatchEvent(new Event('change'));
  });

  // Dispara carregamento automático se já houver valores preenchidos
  window.addEventListener('load', function () {
    if (select.value && document.getElementById('filtro_mes').value) {
      select.dispatchEvent(new Event('change'));
    }
  });
});

// Função global para marcar fatura como paga
function marcarComoPaga(id) {
  if (!confirm("Deseja realmente marcar a fatura como paga?")) return;

  fetch('backend/marcar_fatura_paga.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `fatura_id=${id}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.sucesso) {
        alert('Fatura marcada como paga!');
        document.getElementById('filtro_cartao').dispatchEvent(new Event('change'));
      } else {
        alert('Erro: ' + data.erro);
      }
    })
    .catch(() => alert('Erro ao processar a solicitação.'));
}
