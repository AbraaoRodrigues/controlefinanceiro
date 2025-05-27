document.addEventListener('DOMContentLoaded', () => {
  const tabela = document.getElementById('tabela-contas');
  const form   = document.getElementById('form-conta');
  const idInput= document.getElementById('id');
  const valorInput = document.getElementById('saldo_inicial');

  // Aplica máscara de moeda no campo saldo_inicial
  valorInput.addEventListener('input', function () {
    let v = this.value.replace(/\D/g, "");
    v = (parseInt(v, 10) / 100).toFixed(2);
    this.value = 'R$ ' + v.replace('.', ',');
  });

  document.getElementById("btnNovaConta").addEventListener('click', () => {
  document.getElementById("form-conta").reset();
  document.getElementById("id").value = ""; // <-- essa linha é essencial
});

  form.addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(form);
    fd.append('acao','salvar');
    fetch('backend/contas_controller.php',{method:'POST',body:fd})
      .then(r=>r.json()).then(d=>{
        alert(d.message); if(d.success){bootstrap.Modal
           .getInstance(document.getElementById('modalConta')).hide();
           carregar();}});
  });

  tabela.addEventListener('click', e => {
    const btnEdit = e.target.closest('.editar-conta');
    const btnDel  = e.target.closest('.excluir-conta');
    if (btnEdit) preencherModal(JSON.parse(btnEdit.dataset.conta));
    if (btnDel)  excluir(btnDel.dataset.id);
  });

  function preencherModal(ct){
    idInput.value            = ct.id;
    form.nome_conta.value    = ct.nome_conta;
    form.banco.value         = ct.banco;
    form.saldo_inicial.value = ct.saldo_inicial;
    new bootstrap.Modal(document.getElementById('modalConta')).show();
  }

  function excluir(id){
    if(!confirm('Excluir / Arquivar conta?')) return;
    const fd=new FormData(); fd.append('acao','excluir'); fd.append('id',id);
    fetch('backend/contas_controller.php',{method:'POST',body:fd})
      .then(r=>r.json()).then(d=>{alert(d.message); if(d.success) carregar();});
  }

  function carregar(){
    fetch('backend/contas_controller.php',{method:'POST',
          headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body:'acao=buscar'})
      .then(r=>r.json()).then(d=>{
        tabela.innerHTML='';
        if(!d.success){tabela.innerHTML='<tr><td colspan=\"5\">Erro</td></tr>';return;}
        d.dados.forEach(c=>{
          const tr=document.createElement('tr');
          tr.innerHTML=`
            <td>${c.nome_conta}</td>
            <td>${c.banco}</td>
            <td>R$ ${parseFloat(c.saldo_inicial).toLocaleString('pt-BR',{minimumFractionDigits:2})}</td>
            <td><span class="badge ${c.status==='Ativa'?'bg-success':'bg-secondary'}">${c.status}</span></td>
            <td>
              <button class="btn btn-sm btn-outline-primary editar-conta"
                      data-conta='${JSON.stringify(c)}'>Editar</button>
              <button class="btn btn-sm btn-outline-danger excluir-conta" data-id="${c.id}">Excluir</button>
            </td>`;
          tabela.appendChild(tr);
        });
      });
  }
  carregar();
});
