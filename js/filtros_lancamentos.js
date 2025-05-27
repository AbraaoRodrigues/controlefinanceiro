document.addEventListener('DOMContentLoaded', () => {
  const formFiltro = document.getElementById('form-filtros-lancamentos');

  if (formFiltro) {
    formFiltro.addEventListener('submit', e => {
      e.preventDefault();
      const filtros = {
        inicio: document.getElementById('filtro-inicio').value,
        fim: document.getElementById('filtro-fim').value,
        tipo: document.getElementById('filtro-tipo').value,
        categoria: document.getElementById('filtro-categoria').value,
        status: document.getElementById('filtro-status').value
      };
      carregarLancamentos(filtros);
    });
  }

  // carregar lançamentos ao iniciar a página
  carregarLancamentos();
});
