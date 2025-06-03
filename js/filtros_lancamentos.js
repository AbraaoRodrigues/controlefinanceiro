document.addEventListener('DOMContentLoaded', () => {
  const formFiltro = document.getElementById('form-filtros-lancamentos');

  // Se o formulário existir, tratamos o submit dos filtros
  if (formFiltro) {
    formFiltro.addEventListener('submit', e => {
      e.preventDefault();

      const getVal = id => {
        const el = document.getElementById(id);
        return el ? el.value : '';
      };

      const filtros = {
        inicio: getVal('filtro-inicio'),
        fim: getVal('filtro-fim'),
        tipo: getVal('filtro-tipo'),
        categoria: getVal('filtro-categoria'),
        status: getVal('filtro-status')
      };

      carregarLancamentos(filtros);
    });
  }

  // Carregamento inicial com range de -15 até +15 dias
  const hoje = new Date();
  const inicio = new Date(hoje);
  const fim = new Date(hoje);

  inicio.setDate(hoje.getDate() - 15);
  fim.setDate(hoje.getDate() + 15);

  const formatarData = data => data.toISOString().slice(0, 10);

  carregarLancamentos({
    inicio: formatarData(inicio),
    fim: formatarData(fim)
  });
});
