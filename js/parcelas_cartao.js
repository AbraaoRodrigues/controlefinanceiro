document.addEventListener('DOMContentLoaded', function () {
  const select = document.getElementById('filtro_cartao');
  const lista = document.getElementById('lista-compras');

  // Verifica se estamos na página correta
  if (!select || !lista) return;

  const resumo = document.getElementById('resumo-fatura');
  const total = document.getElementById('total-fatura');
  const pendente = document.getElementById('valor-pendente');
  if (resumo && total && pendente) {
    resumo.style.display = 'none';
    total.textContent = 'R$ 0,00';
    pendente.textContent = 'R$ 0,00';
  }

  select.addEventListener('change', function () {
    const id = this.value;
    lista.innerHTML = '';
    if (resumo) resumo.style.setProperty('display', 'none');
    if (!id) return;

    fetch(`backend/parcelas_cartao.php?cartao_id=${id}`)
      .then(res => res.json())
      .then(data => {
        if (data.success && data.dados.length > 0) {
          let html = '';
          let ultimaCompra = null;
          let contador = 0;
          let totalFatura = 0;
          let totalPendente = 0;

          data.dados.forEach(parcela => {
            totalFatura += parseFloat(parcela.valor);
            if (parcela.pago != 1) totalPendente += parseFloat(parcela.valor);

            if (ultimaCompra !== parcela.compra_id) {
              if (contador > 0) html += '</tbody></table><br>';

              html += `
                <div class="card mb-3">
                  <div class="card-header bg-primary text-white">
                    <strong>${parcela.descricao}</strong> - Valor Total: R$ ${parseFloat(parcela.valor_total).toFixed(2).replace('.', ',')} (${parcela.parcelas}x)
                  </div>
                  <div class="card-body p-0">
                    <table class="table table-sm m-0">
                      <thead>
                        <tr>
                          <th>Parcela</th>
                          <th>Valor</th>
                          <th>Vencimento</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
              `;
              ultimaCompra = parcela.compra_id;
              contador++;
            }

            html += `
              <tr>
                <td>${parcela.numero}/${parcela.parcelas}</td>
                <td>R$ ${parseFloat(parcela.valor).toFixed(2).replace('.', ',')}</td>
                <td>${parcela.vencimento}</td>
                <td>
                  <span class="badge bg-${parcela.pago == 1 ? 'success' : 'secondary'}">
                    ${parcela.pago == 1 ? 'Pago' : 'Pendente'}
                  </span>
                </td>
              </tr>
            `;
          });

          html += '</tbody></table></div></div>';
          lista.innerHTML = html;

          if (resumo && total && pendente) {
            resumo.style.display = 'flex';
            total.textContent = totalFatura.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            pendente.textContent = totalPendente.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
          }

        } else {
          lista.innerHTML = '<div class="alert alert-info">Nenhuma compra encontrada para este cartão.</div>';
        }
      })
      .catch(() => {
        lista.innerHTML = '<div class="alert alert-danger">Erro ao carregar as parcelas.</div>';
      });
  });
});
