let chartBarras = null;
let chartPizza = null;

document.addEventListener("DOMContentLoaded", function () {
    // Seleciona o formulário de filtro
    const formFiltro = document.getElementById("filtro-form");

    // Captura o evento de envio para construir a query string
    formFiltro.addEventListener("submit", function (e) {
        e.preventDefault(); // Impede o comportamento padrão do form

        // Pega os valores de data
        const dataInicio = document.getElementById("data-inicio").value;
        const dataFim = document.getElementById("data-fim").value;

        // Monta a query string (ex: "?inicio=2023-01-01&fim=2023-01-31")
        let queryString = "";
        if (dataInicio) {
            queryString += `?inicio=${dataInicio}`;
        }
        if (dataFim) {
            // Se já tem "?" na queryString, usa "&"
            queryString += (queryString ? "&" : "?") + `fim=${dataFim}`;
        }

        // Chama a função que carrega dados para o Dashboard, com os parâmetros
        carregarDashboard(queryString);
    });

    // Carrega inicialmente sem filtro (tudo)
    carregarDashboard("");

    /**
     * Faz o fetch em dados_graficos.php, atualiza indicadores e gráficos
     */
    function carregarDashboard(queryString) {
        // Ajuste "../backend/dados_graficos.php" conforme sua estrutura
        fetch("../backend/dados_graficos.php" + queryString)
            .then(response => response.json())
            .then(data => {
                // Atualizar indicadores
                atualizarIndicadores(data);

                // Atualizar gráficos
                atualizarGraficos(data);
            })
            .catch(error => console.error("Erro ao carregar os dados:", error));
    }

    /**
     * Atualiza os indicadores (entradas, saídas, saldo, a vencer)
     */
    function atualizarIndicadores(data) {
        const formatBRL = valor => valor.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
        document.getElementById("total-entrada").textContent = formatBRL(data.total_entradas);
        document.getElementById("total-saida").textContent   = formatBRL(data.total_saidas);
        document.getElementById("saldo-atual").textContent   = formatBRL(data.saldo_atual);
        document.getElementById("total-vencer").textContent  = formatBRL(data.total_vencer);
    }

    /**
     * Atualiza os gráficos de barras e pizza
     */
    function atualizarGraficos(data) {
        // Gráfico de Barras
        if (chartBarras) chartBarras.destroy();
        const ctxBarras = document.getElementById("graficoBarras").getContext("2d");
        chartBarras = new Chart(ctxBarras, {
            type: "bar",
            data: {
                labels: data.meses,
                datasets: [
                    {
                        label: "Entradas",
                        data: data.entradas,
                        backgroundColor: "green"
                    },
                    {
                        label: "Saídas",
                        data: data.saidas,
                        backgroundColor: "red"
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 14,
                                family: 'Arial'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Evolução de Entradas e Saídas',
                        font: {
                            size: 14,
                            family: 'Arial'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: 13,
                                family: 'Arial'
                            }
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                size: 13,
                                family: 'Arial'
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de Pizza
        if (chartPizza) chartPizza.destroy();
        const ctxPizza = document.getElementById("graficoPizza").getContext("2d");
        chartPizza = new Chart(ctxPizza, {
  type: "pie",
  data: {
    labels: data.categorias,
    datasets: [{
      data: data.valores,
      backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50"]
    }]
  },
  // Aqui informamos que usaremos o plugin datalabels
  plugins: [ChartDataLabels], 
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      // Configurações do plugin datalabels
      datalabels: {
        formatter: function (value, context) {
          // Somatória total dos valores
          let total = context.dataset.data.reduce((acc, val) => acc + val, 0);
          // Calcula a porcentagem
          let percentage = ((value / total) * 100).toFixed(1) + '%';
          return percentage;
        },
        color: '#fff',
        font: {
          size: 14
        }
      },
      // Configurações da legenda
      legend: {
        labels: {
          font: {
            size: 14,
            family: 'Arial'
          }
        }
      },
      // Configurações do título
      title: {
        display: true,
        text: 'Distribuição por Categoria',
        font: {
          size: 16,
          family: 'Arial'
        }
      }
    }
  }
});
}
});