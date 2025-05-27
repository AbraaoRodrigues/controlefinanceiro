document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('form-compra-cartao');
  const valorInput = document.getElementById('valor_total');

  // Máscara de dinheiro
  valorInput.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, "");
    v = (parseInt(v, 10) / 100).toFixed(2);
    this.value = 'R$ ' + v.replace('.', ',');
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch('backend/salvar_compra_cartao.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          form.reset();
        } else {
          alert('Erro: ' + data.message);
        }
      })
      .catch(() => alert('Erro ao comunicar com o servidor.'));
  });
});
