
document.addEventListener("DOMContentLoaded", function () {
    let formLancamento = document.getElementById("form-lancamento");

    if (formLancamento) {
        formLancamento.addEventListener("submit", function (event) {
            event.preventDefault(); // Impede o envio padrão do formulário

            // Pegando o valor numérico sem máscara
            let valorInput = document.querySelector(".campo-valor");
            if (!valorInput) {
                alert("Erro: campo de valor não encontrado!");
                return;
            }

            let mask = IMask(valorInput, {
                mask: 'R$ num',
                blocks: {
                    num: {
                        mask: Number,
                        scale: 2,
                        thousandsSeparator: '.',
                        radix: ',',
                        normalizeZeros: true,
                        padFractionalZeros: true
                    }
                }
            });

            let valorFormatado = mask.unmaskedValue; // Ex: "R$ 1.234,56" → "1234.56"

            // Sincroniza a máscara antes de enviar
            mask.updateValue();

            let formData = new FormData(this);
            formData.set("valor", valorFormatado); // Substitui o valor mascarado pelo correto

            fetch("../backend/adicionar_lancamento.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Lançamento registrado com sucesso!");
                    formLancamento.reset(); // Limpa o formulário
                    mask.updateValue(); // Sincroniza a máscara corretamente após limpar
                } else {
                    alert("Erro ao registrar: " + (data.error || "Erro desconhecido"));
                }
            })
            .catch(error => {
                console.error("Erro ao enviar requisição:", error);
                alert("Erro ao processar requisição.");
            });
        });
    } else {
        console.error("Elemento 'form-lancamento' não encontrado!");
    }
});


document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        aplicarMascaraNosCampos();
    }, 500); // Pequeno delay para garantir que os elementos existam
});


document.addEventListener("DOMContentLoaded", function () {
    fetch("../backend/buscar_categorias.php")
        .then(response => response.json())
        .then(data => {
            let categoriaSelect = document.getElementById("categoria");
            categoriaSelect.innerHTML = '<option value="">Selecione</option>'; 

            function adicionarOpcoes(categorias, prefixo = "") {
                categorias.forEach(categoria => {
                    let option = document.createElement("option");
                    option.value = categoria.id;
                    option.textContent = prefixo + categoria.categoria;
                    
                    // Deixa as categorias pai em negrito (apenas visualmente, sem afetar a estrutura)
                    if (categoria.nivel === 0) {
                        option.style.fontWeight = "bold";
                    }

                    categoriaSelect.appendChild(option);

                    // Se houver subcategorias, chamamos a função recursivamente com um prefixo maior
                    if (categoria.subcategorias && categoria.subcategorias.length > 0) {
                        adicionarOpcoes(categoria.subcategorias, prefixo + "— "); // Prefixo para indicar hierarquia
                    }
                });
            }

            adicionarOpcoes(data); // Chama a função para adicionar opções

        })
        .catch(error => {
            console.error("Erro ao buscar categorias:", error);
            document.getElementById("categoria").innerHTML = '<option value="">Erro ao carregar</option>';
        });
});

document.getElementById("voltarPagina").addEventListener("click", function () {
    window.location.href = "../pages/lancamentos.php"; // Redireciona para a página desejada
});
