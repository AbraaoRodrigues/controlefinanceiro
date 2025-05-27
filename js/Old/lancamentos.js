document.addEventListener("DOMContentLoaded", function () {
    const lancamentosTable = document.getElementById("lancamentosTable");
    const totalValorElement = document.getElementById("totalValor");
    const btnMinimizarFiltros = document.getElementById("btnMinimizarFiltros");
    const filtrosContainer = document.getElementById("filtrosContainer");

    async function fetchLancamentos() {
        try {
            const response = await fetch("../backend/buscar_lancamentos.php");
            const lancamentos = await response.json();

            if (lancamentos.error) {
                alert("Erro ao buscar lançamentos: " + lancamentos.error);
                return;
            }

            let total = 0;
            if (lancamentosTable) {
                lancamentosTable.innerHTML = ""; // Limpa a tabela antes de preencher

                lancamentos.forEach((lancamento) => {
                    const row = document.createElement("tr");

                    // Define a cor do valor (verde para entrada, vermelho para saída)
                    let valorClass = lancamento.tipo === "Receita" ? "credit" : "debit";

                    // Se for pendente, adiciona a classe pending
                    if (lancamento.status === "Pendente") {
                        row.classList.add("pending");
                    }

const [ano, mes, dia] = lancamento.data.split("-");
const dataFormatada = `${dia}/${mes}/${ano}`;

row.innerHTML = `
    <td>${new Date(lancamento.data).toLocaleDateString()}</td>
    <td>${lancamento.categoria || "Sem categoria"}</td>
    <td>${lancamento.descricao}</td>
    <td class="${valorClass}">R$ ${parseFloat(lancamento.valor).toFixed(2)}</td>
    <td>${lancamento.status}</td>
    <td>
        <button class="editar-btn" data-id="${lancamento.id}" title="Editar" onclick="editarLancamento(this)">
            <i class="fas fa-edit"></i>
        </button>
        <button class="excluir-btn" data-id="${lancamento.id}" title="Excluir" onclick="excluirLancamento(${lancamento.id})">
            <i class="fas fa-trash-alt"></i>
        </button>
    </td>
`;

                    lancamentosTable.appendChild(row);

                    // Soma entradas e subtrai saídas (independente do status)
                    if (lancamento.tipo === "Receita") {
                        total += parseFloat(lancamento.valor);
                    } else if (lancamento.tipo === "Despesa") {
                        total -= parseFloat(lancamento.valor);
                    }
                });
            }

            // Atualiza o totalizador com bold
            if (totalValorElement) {
                totalValorElement.innerHTML = `<strong>R$ ${total.toFixed(2)}</strong>`;
            }
        } catch (error) {
            console.error("Erro ao buscar lançamentos:", error);
        }
    }

    // Função para minimizar filtros
    if (btnMinimizarFiltros && filtrosContainer) {
        btnMinimizarFiltros.addEventListener("click", function () {
            filtrosContainer.classList.toggle("d-none");
        });
    }

    // Busca os lançamentos ao carregar a página
    fetchLancamentos();

    // Botão para abrir página de lançamento
    const btnAdicionarLancamento = document.getElementById("abrirPaginaLancamento");
    if (btnAdicionarLancamento) {
        btnAdicionarLancamento.addEventListener("click", function () {
            window.location.href = "../pages/cadastro_lancamento.php"; // Altere para o nome correto da página
        });
    }
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

// Função global para buscar lançamentos
async function fetchLancamentos(queryString = "") {
    try {
        const response = await fetch("../backend/buscar_lancamentos.php?" + queryString);
        const lancamentos = await response.json();

        if (lancamentos.error) {
            alert("Erro ao buscar lançamentos: " + lancamentos.error);
            return;
        }

        let total = 0;
        const lancamentosTable = document.getElementById("lancamentosTable");
        const totalValorElement = document.getElementById("totalValor");

        if (lancamentosTable) {
            lancamentosTable.innerHTML = ""; // Limpa a tabela antes de preencher

            lancamentos.forEach((lancamento) => {
                const row = document.createElement("tr");

                let valorClass = lancamento.tipo === "Receita" ? "credit" : "debit";

                if (lancamento.status === "Pendente") {
                    row.classList.add("pending");
                }

                row.innerHTML = `
                    <td>${new Date(lancamento.data).toLocaleDateString()}</td>
                    <td>${lancamento.categoria || "Sem categoria"}</td>
                    <td>${lancamento.descricao}</td>
                    <td class="${valorClass}">R$ ${parseFloat(lancamento.valor).toFixed(2)}</td>
                    <td>${lancamento.status}</td>
                    <td>
                        <button onclick="editarLancamento(this)" data-id="${lancamento.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="excluirLancamento(${lancamento.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;

                lancamentosTable.appendChild(row);

                if (lancamento.tipo === "Receita") {
                    total += parseFloat(lancamento.valor);
                } else if (lancamento.tipo === "Despesa") {
                    total -= parseFloat(lancamento.valor);
                }
            });
        }

        if (totalValorElement) {
            totalValorElement.innerHTML = `<strong>R$ ${total.toFixed(2)}</strong>`;
        }
    } catch (error) {
        console.error("Erro ao buscar lançamentos:", error);
    }
}

// Captura os filtros e chama fetchLancamentos
const filterForm = document.getElementById("filterForm");
if (filterForm) {
    filterForm.addEventListener("submit", function (event) {
        event.preventDefault(); // Evita o recarregamento da página

        // Captura os valores dos filtros
        const categoria = document.getElementById("categoria").value;
        const dataInicio = document.getElementById("dataInicio").value;
        const dataFim = document.getElementById("dataFim").value;
        const valorMin = document.getElementById("valorMin").value;
        const valorMax = document.getElementById("valorMax").value;
        const status = document.getElementById("status").value;

        // Monta a query string para enviar ao backend
        const queryString = new URLSearchParams({
            categoria,
            dataInicio,
            dataFim,
            valorMin,
            valorMax,
            status,
        }).toString();

        // Chamar a função fetchLancamentos passando a query string
        fetchLancamentos(queryString);
    });
} else {
    console.error("Elemento 'filterForm' não encontrado!");
}

// Botão de limpar filtros - Resetar o formulário e recarregar a lista completa
document.getElementById("limparFiltros").addEventListener("click", function () {
    document.getElementById("filterForm").reset();
    fetchLancamentos(); // Recarrega os lançamentos sem filtros
});


document.addEventListener("DOMContentLoaded", function () {
    let filterForm = document.getElementById("filterForm");

    if (filterForm) {
        filterForm.addEventListener("submit", function (event) {
            event.preventDefault(); // Evita o envio padrão do formulário

            // Captura os valores dos filtros
            const categoria = document.getElementById("categoria").value;
            const dataInicio = document.getElementById("dataInicio").value;
            const dataFim = document.getElementById("dataFim").value;
            const valorMin = document.getElementById("valorMin").value;
            const valorMax = document.getElementById("valorMax").value;
            const status = document.getElementById("status").value;

            // Monta a query string para enviar ao backend
            const queryString = new URLSearchParams({
                categoria,
                dataInicio,
                dataFim,
                valorMin,
                valorMax,
                status,
            }).toString();

            // Chamar a função fetchLancamentos passando a query string
            fetchLancamentos(queryString);
        });
    } else {
        console.error("Elemento 'filterForm' não encontrado!");
    }

    // Botão de limpar filtros - Resetar o formulário e recarregar a lista completa
    document.getElementById("limparFiltros").addEventListener("click", function () {
        document.getElementById("filterForm").reset();
        fetchLancamentos(); // Recarrega os lançamentos sem filtros
    });
});



document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        aplicarMascaraNosCampos();
    }, 500); // Pequeno delay para garantir que os elementos existam
});

document.querySelectorAll(".editar-btn").forEach((button) => {
    button.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        editarLancamento(id);
    });
});

document.querySelectorAll(".excluir-btn").forEach((button) => {
    button.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        excluirLancamento(id);
    });
});

async function carregarCategorias(select, categoriaSelecionada = null) {
    try {
        let resposta = await fetch("../backend/buscar_categorias.php");
        let categorias = await resposta.json();

        if (!categorias || categorias.length === 0) {
            console.warn("Nenhuma categoria encontrada.");
            return;
        }

        // Construir as opções
        let opcoes = `<option value="">Selecione uma categoria</option>`;
        opcoes += montarOpcoesCategorias(categorias, categoriaSelecionada);

        select.innerHTML = opcoes;

    } catch (erro) {
        console.error("Erro ao carregar categorias:", erro);
    }
}

function montarOpcoesCategorias(categorias, categoriaAtual, nivel = 0) {
    let opcoes = "";
    categorias.forEach(cat => {
        let prefixo = "— ".repeat(nivel); // Adiciona indentação visual
        let selecionado = cat.categoria === categoriaAtual ? "selected" : "";
        opcoes += `<option value="${cat.id}" ${selecionado}>${prefixo}${cat.categoria}</option>`;

        if (cat.subcategorias.length > 0) {
            opcoes += montarOpcoesCategorias(cat.subcategorias, categoriaAtual, nivel + 1);
        }
    });
    return opcoes;
}

function editarLancamento(botao) {
    let row = botao.closest("tr");
    let id = botao.getAttribute("data-id");

    if (!id) {
        console.error("Erro: ID do lançamento não encontrado.");
        return;
    }

    // Pegando os valores antigos da linha
    let dataOriginalBr = row.children[0].innerText.trim();
    let partesData = dataOriginalBr.split("/");
let dataOriginal = `${partesData[2]}-${partesData[1]}-${partesData[0]}`; // yyyy-MM-dd
    let categoriaOriginal = row.children[1].innerText.trim();
    let descricaoOriginal = row.children[2].innerText.trim();
    let valorOriginal = row.children[3].innerText.replace("R$ ", "").trim();
    let statusOriginal = row.children[4].innerText.trim();

    // Armazena os valores originais nos atributos do botão
    botao.setAttribute("data-original-data", dataOriginal);
    botao.setAttribute("data-original-categoria", categoriaOriginal);
    botao.setAttribute("data-original-descricao", descricaoOriginal);
    botao.setAttribute("data-original-valor", valorOriginal);
    botao.setAttribute("data-original-status", statusOriginal);

    // Converte a linha para inputs editáveis
    row.innerHTML = `
        <td><input type="date" class="input-data" value="${dataOriginal}"></td>
        <td>
            <select class="input-categoria"></select> 
        </td>
        <td><input type="text" class="input-descricao" value="${descricaoOriginal}"></td>
        <td><input type="text" class="input-valor" value="${valorOriginal}"></td>
        <td>
            <select class="input-status">
                <option value="Pago" ${statusOriginal === "Pago" ? "selected" : ""}>Pago</option>
                <option value="Pendente" ${statusOriginal === "Pendente" ? "selected" : ""}>Pendente</option>
            </select>
        </td>
        <td>
            <button onclick="salvarEdicao(this)" class="salvar-btn" data-id="${id}">
                <i class="fas fa-save"></i>
            </button>
            <button onclick="cancelarEdicao(this)" class="cancelar-btn" data-id="${id}"
                data-original-data="${dataOriginal}"
                data-original-categoria="${categoriaOriginal}"
                data-original-descricao="${descricaoOriginal}"
                data-original-valor="${valorOriginal}"
                data-original-status="${statusOriginal}">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;

    // Obtém a referência ao <select> recém-criado
    let selectCategoria = row.querySelector(".input-categoria");

    // Carrega as categorias e seleciona a original corretamente
    carregarCategorias(selectCategoria, categoriaOriginal);
}

function formatarDataParaInput(data) {
    let partes = data.split("/");
    return `${partes[2]}-${partes[1]}-${partes[0]}`;
}

async function salvarEdicao(botao) {
    let row = botao.closest("tr");
    let id = botao.getAttribute("data-id");

    if (!id) {
        console.error("Erro: ID do lançamento não encontrado.");
        return;
    }

    // Capturar a data corretamente do input
    let dataInput = row.querySelector(".input-data").value;

    // Garantir que a data não sofra alteração de fuso horário
    let dataFormatada = new Date(dataInput + "T00:00:00").toISOString().split("T")[0];

    let selectCategoria = row.querySelector(".input-categoria");

    let dadosAtualizados = {
        id: id,
        data: dataFormatada, // Usa a data corrigida
        categoria: selectCategoria.value,
        descricao: row.querySelector(".input-descricao").value.trim(),
        valor: row.querySelector(".input-valor").value.trim(),
        status: row.querySelector(".input-status").value
    };

    try {
        let resposta = await fetch("../backend/atualiza_lancamento.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(dadosAtualizados)
        });

        let resultado = await resposta.json();

        if (resultado.sucesso) {

            // Atualizar a linha da tabela sem recarregar a página
            row.innerHTML = `
                <td>${dataInput.split("-").reverse().join("/")}</td>
                <td>${selectCategoria.options[selectCategoria.selectedIndex].text}</td>
                <td>${dadosAtualizados.descricao}</td>
                <td>R$ ${parseFloat(dadosAtualizados.valor).toFixed(2)}</td>
                <td>${dadosAtualizados.status}</td>
                <td>
                    <button onclick="editarLancamento(this)" class="editar-btn" data-id="${id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="excluirLancamento(${id})" class="excluir-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
        } else {
            console.error("Erro ao atualizar:", resultado.erro);
        }
    } catch (erro) {
        console.error("Erro na requisição:", erro);
    }
}

function cancelarEdicao(botao) {
    let row = botao.closest("tr");

    let dataOriginal = botao.getAttribute("data-original-data");
    let categoriaOriginal = botao.getAttribute("data-original-categoria");
    let descricaoOriginal = botao.getAttribute("data-original-descricao");
    let valorOriginal = botao.getAttribute("data-original-valor");
    let statusOriginal = botao.getAttribute("data-original-status");
    let id = botao.getAttribute("data-id");

    // Converte yyyy-MM-dd de volta para dd/MM/yyyy
    let partesData = dataOriginal.split("-");
    let dataFormatada = `${partesData[2]}/${partesData[1]}/${partesData[0]}`;

    row.innerHTML = `
        <td>${dataFormatada}</td>
        <td>${categoriaOriginal}</td>
        <td>${descricaoOriginal}</td>
        <td>R$ ${parseFloat(valorOriginal).toFixed(2)}</td>
        <td>${statusOriginal}</td>
        <td>
            <button class="editar-btn" data-id="${id}" title="Editar" onclick="editarLancamento(this)">
                <i class="fas fa-edit"></i>
            </button>
            <button class="excluir-btn" data-id="${id}" title="Excluir">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    `;
}

document.addEventListener("click", function (event) {
    if (event.target.classList.contains("btn-excluir")) {
        const id = event.target.getAttribute("data-id");

        if (confirm("Tem certeza que deseja excluir este lançamento?")) {
            excluirLancamento(id);
        }
    }
});

// Função para excluir um lançamento
function excluirLancamento(id) {
    fetch("../backend/excluir_lancamento.php", {
        method: "POST",
        body: JSON.stringify({ id }),
        headers: { "Content-Type": "application/json" }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Lançamento excluído com sucesso!");
            fetchLancamentos(); // Atualiza a lista
        } else {
            alert("Erro ao excluir: " + (data.error || "Erro desconhecido"));
        }
    })
    .catch(error => {
        console.error("Erro ao excluir lançamento:", error);
        alert("Erro ao processar a requisição.");
    });
}
