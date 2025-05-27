document.addEventListener("DOMContentLoaded", function () {
  const tabela = document.getElementById("tabela-lancamentos");

  // Delegação para botão editar
  tabela.addEventListener("click", function (e) {
    if (e.target.closest(".editar-lancamento")) {
      const id = e.target.closest(".editar-lancamento").dataset.id;
      editarLancamento(id);
    }
  });

  // Delegação para botão excluir
  tabela.addEventListener("click", function (e) {
    if (e.target.closest(".excluir-lancamento")) {
      const id = e.target.closest(".excluir-lancamento").dataset.id;
      if (confirm("Tem certeza que deseja excluir este lançamento?")) {
        excluirLancamento(id);
      }
    }
  });
});

function editarLancamento(id) {
  fetch(`backend/obter_lancamento.php?id=${id}`)
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const l = data.dados;
document.getElementById('id').value          = l.id;
document.getElementById('descricao').value   = l.descricao;
document.getElementById('tipo_lancamento').value = l.tipo;           // <-- id correto
document.getElementById('categoria_id').value   = l.categoria_id || '';
document.getElementById('valor').value       = (l.valor * 1).toLocaleString('pt-BR', {minimumFractionDigits: 2});
document.getElementById('data').value        = l.data;
document.getElementById('status').value      = l.status;

        const modal = new bootstrap.Modal(document.getElementById("modalNovo"));
        modal.show();
      } else {
        alert(data.message);
      }
    })
    .catch(err => {
      alert("Erro ao carregar lançamento para edição.");
      console.error(err);
    });
}

function excluirLancamento(id) {
  fetch("backend/excluir_lancamento.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `id=${id}`
  })
    .then(res => res.json())
    .then(data => {
      alert(data.message);
      if (data.success) carregarLancamentos();
    })
    .catch(err => {
      alert("Erro ao excluir lançamento.");
      console.error(err);
    });
}
