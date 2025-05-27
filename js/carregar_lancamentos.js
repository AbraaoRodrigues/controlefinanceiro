document.addEventListener("DOMContentLoaded", function () {
  carregarLancamentos();
  const tipoSelect = document.getElementById("tipo_lancamento");
  if (tipoSelect) {
    // Carrega as categorias do tipo selecionado quando o modal abre
    carregarCategoriasNoFormulario(tipoSelect.value);

    // Atualiza categorias sempre que mudar Receita/Despesa
    tipoSelect.addEventListener("change", () => {
      carregarCategoriasNoFormulario(tipoSelect.value);
    });
  }
  const valorInput = document.getElementById('valor');

  // Aplica máscara de moeda no campo saldo_inicial
  valorInput.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, "");
    v = (parseInt(v, 10) / 100).toFixed(2);
    this.value = 'R$ ' + v.replace('.', ',');
  });

});

function carregarLancamentos(filtros = {}) {
  const params = new URLSearchParams(filtros);

  fetch(`backend/buscar_lancamentos.php?${params.toString()}`)
    .then(response => response.json())
    .then(data => {
      console.log("Dados recebidos:", data);

      const tbody = document.getElementById("tabela-lancamentos");
      tbody.innerHTML = "";

      if (!data.success || !Array.isArray(data.dados)) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">Nenhum lançamento encontrado.</td></tr>`;
        return;
      }

      data.dados.forEach(l => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
          <td>${l.descricao}</td>
          <td>${l.tipo}</td>
          <td>${l.categoria ?? "-"}</td>
          <td>R$ ${parseFloat(l.valor).toLocaleString('pt-BR', { style: 'decimal', minimumFractionDigits: 2 })}</td>
          <td>${new Date(l.data).toLocaleDateString('pt-BR')}</td>
          <td>
            <span class="badge ${l.status === 'Pago' ? 'bg-success' : 'bg-warning text-dark'}">
              ${l.status}
            </span>
          </td>
          <td>
            <button class="btn btn-sm btn-outline-primary editar-lancamento" data-id="${l.id}"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger excluir-lancamento" data-id="${l.id}"><i class="fas fa-trash"></i></button>
          </td>
        `;

        tbody.appendChild(tr);
      });
    })
    .catch(error => {
      console.error("Erro ao carregar os lançamentos:", error);
      const tbody = document.getElementById("tabela-lancamentos");
      tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Erro ao carregar dados.</td></tr>`;
    });
}

function carregarCategoriasNoFormulario(tipo) {
  fetch("backend/categorias_controller.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "acao=buscar"
  })
    .then(r => r.json())
    .then(d => {
      if (!d.success) return;

      const select = document.getElementById("categoria_id");
      select.innerHTML = '<option value="">Selecione</option>';

      const categorias = d.dados.filter(c => c.tipo === tipo && Number(c.pai) === 1);
      categorias.forEach(pai => {
        const optGroup = document.createElement("optgroup");
        optGroup.label = pai.categoria;

        d.dados
          .filter(f => f.tipo === tipo && Number(f.pai) === 0 && f.categoria_pai_id == pai.id)
          .forEach(filho => {
            const opt = document.createElement("option");
            opt.value = filho.id;
            opt.textContent = filho.categoria;
            optGroup.appendChild(opt);
          });

        // Só adiciona se houver filhos
        if (optGroup.children.length > 0) {
          select.appendChild(optGroup);
        }
      });
    });
}
