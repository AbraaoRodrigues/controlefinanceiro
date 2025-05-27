document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-cartao');
  const limiteInput = document.getElementById('limite');

  // Máscara de moeda simples
  limiteInput.addEventListener('input', function () {
    let valor = this.value.replace(/\D/g, '');
    valor = (parseFloat(valor) / 100).toFixed(2);
    this.value = 'R$ ' + valor.replace('.', ',');
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch('backend/salvar_cartao.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert('Erro: ' + data.message);
      }
    })
    .catch(() => alert('Erro ao comunicar com o servidor.'));
  });
});
