let chartBarras = null;
let chartPizza = null;

document.addEventListener("DOMContentLoaded", function () {
    console.log("Carregando dados para os gráficos...");

    fetch("../backend/dados_graficos.php")
    .then(response => response.json())
    .then(data => {
        // data.meses, data.entradas e data.saidas
        // data.categorias, data.valores
        
        // 1️⃣ Atualizar Indicadores
      // Formatar como moeda (opcional)
      const formatBRL = valor => valor.toLocaleString("pt-BR", {
        style: "currency", currency: "BRL"
      });

      document.getElementById("total-entrada").textContent = formatBRL(data.total_entradas);
      document.getElementById("total-saida").textContent   = formatBRL(data.total_saidas);
      document.getElementById("saldo-atual").textContent   = formatBRL(data.saldo_atual);
      document.getElementById("total-vencer").textContent  = formatBRL(data.total_vencer);

        // Gráfico de Barras
        const ctxBarras = document.getElementById("graficoBarras").getContext("2d");
        if (chartBarras) { chartBarras.destroy(); }
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

        // Configurações de plugins e legendas
        plugins: {
            legend: {
                labels: {
                    font: {
                        size: 14,    // Tamanho da fonte das legendas
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
        
        // Configurações dos eixos
        scales: {
            x: {
                ticks: {
                    font: {
                        size: 13,   // Tamanho da fonte dos rótulos do eixo X
                        family: 'Arial'
                    }
                }
            },
            y: {
                ticks: {
                    font: {
                        size: 13,   // Tamanho da fonte dos rótulos do eixo Y
                        family: 'Arial'
                    }
                }
            }
        }
    }
});

        // Gráfico de Pizza
        const ctxPizza = document.getElementById("graficoPizza").getContext("2d");
        if (chartPizza) { chartPizza.destroy(); }
        chartPizza = new Chart(ctxPizza, {
            type: "pie",
            data: {
                labels: data.categorias,
                datasets: [{
                    data: data.valores,
                    // Cores que você achar melhor
                    backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50"]
                }]
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
                text: 'Distribuição por Categoria',
                font: {
                    size: 16,
                    family: 'Arial'
                }
            }
        }
    }
});
    })
    .catch(error => console.error("Erro ao carregar os dados:", error));
});
