document.addEventListener("DOMContentLoaded", () => {
  carregarCategorias();

  /* --- submissão do formulário --- */
  const form = document.getElementById("form-categoria");
  const idInput = document.getElementById("id");
  const chkPai  = document.getElementById("pai");

  form.addEventListener("submit", e => {
    e.preventDefault();

    const formData = new FormData(form);
    formData.set("acao", "salvar");
    formData.set("pai", chkPai.checked ? 1 : 0);   // 0 ou 1 corretamente

    fetch("backend/categorias_controller.php", {
      method: "POST",
      body: formData
    })
      .then(r => r.json())
      .then(d => {
        alert(d.message);
        if (d.success) {
          bootstrap.Modal.getInstance(document.getElementById("modalCategoria")).hide();
          form.reset();
          chkPai.checked = false;      // garante que abra desmarcado na próxima vez
          idInput.value = "";         // limpa id para não sobrescrever
          carregarCategorias();
        }
      })
      .catch(() => alert("Erro ao salvar categoria."));
  });

  /* --- abre modal limpo quando clicar em Nova Categoria --- */
  document.getElementById("btnNovaCategoria").addEventListener("click", () => {
  form.reset();
  chkPai.checked = false;
  idInput.value = "";
});
});

/* -------------------------------------------------- */
function carregarCategorias() {
  fetch("backend/categorias_controller.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "acao=buscar"
  })
    .then(r => r.json())
    .then(d => {
      const tabela    = document.getElementById("tabela-categorias");
      const selectPai = document.getElementById("categoria_pai");
      tabela.innerHTML = "";
      selectPai.innerHTML = '<option value="">Nenhuma</option>';

      if (!d.success) {
        tabela.innerHTML = '<tr><td colspan="4">Erro ao carregar categorias</td></tr>';
        return;
      }

      const todas  = d.dados;
      const pais   = todas.filter(c => Number(c.pai) === 1);
      const filhos = todas.filter(c => Number(c.pai) === 0);

      /* Renderizar pais e filhos */
      pais.forEach(pai => {
        renderLinha(pai, false);
        filhos.filter(f => f.categoria_pai_id == pai.id)
              .forEach(filho => renderLinha(filho, true));
      });

      /* popular select */
      pais.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.categoria;
        selectPai.appendChild(opt);
      });

      function renderLinha(cat, ehFilho) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td class="${ehFilho ? 'ps-4' : 'fw-bold'}">
            ${ehFilho ? '↳ ' : ''}<span style="color:${cat.cor_texto}">${cat.categoria}</span>
          </td>
          <td>${nomePai(cat.categoria_pai_id)}</td>
          <td><span class="badge" style="background:${cat.cor_texto}">${cat.cor_texto}</span></td>
          <td>
            <button class="btn btn-sm btn-outline-primary" onclick='editarCategoria(${JSON.stringify(cat)})'>Editar</button>
            <button class="btn btn-sm btn-outline-danger"  onclick='excluirCategoria(${cat.id})'>Excluir</button>
          </td>`;
        tabela.appendChild(tr);
      }

      function nomePai(id) {
        if (!id) return '-';
        const pai = todas.find(c => c.id == id);
        return pai ? pai.categoria : '-';
      }
    });
}

/* ---------------- edit / delete helpers ---------------- */
function editarCategoria(cat) {
  document.getElementById('id').value          = cat.id;
  document.getElementById('categoria').value   = cat.categoria;
  document.getElementById("tipo").value = cat.tipo || "Despesa";
  document.getElementById('pai').checked       = Number(cat.pai) === 1;
  document.getElementById('categoria_pai').value = cat.categoria_pai_id || "";
  document.getElementById('cor_texto').value   = cat.cor_texto;
  new bootstrap.Modal(document.getElementById('modalCategoria')).show();
}

function excluirCategoria(id) {
  if (!confirm('Tem certeza que deseja excluir esta categoria?')) return;
  const fd = new FormData();
  fd.append('acao', 'excluir');
  fd.append('id', id);

  fetch('backend/categorias_controller.php', { method:'POST', body: fd })
    .then(r=>r.json())
    .then(d=>{ alert(d.message); if(d.success) carregarCategorias(); })
    .catch(()=>alert('Erro ao excluir categoria.'));
}
