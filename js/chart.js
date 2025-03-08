let chartBarras = null;
let chartPizza = null;

document.addEventListener("DOMContentLoaded", function () {
    console.log("Carregando dados para os gráficos...");

    fetch("../backend/dados_graficos.php")
        .then(response => response.json())
        .then(data => {
            // 1️⃣ Gráfico de Barras (Evolução de Entradas e Saídas)
            var ctxBarras = document.getElementById("graficoBarras").getContext("2d");

            // Se já existir um gráfico, destruí-lo antes de recriar
            if (chartBarras) {
                chartBarras.destroy();
            }

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
                    maintainAspectRatio: false
                }
            });

            // 2️⃣ Gráfico de Pizza (Distribuição de Gastos por Categoria)
            var ctxPizza = document.getElementById("graficoPizza").getContext("2d");

            // Se já existir um gráfico, destruí-lo antes de recriar
            if (chartPizza) {
                chartPizza.destroy();
            }

            chartPizza = new Chart(ctxPizza, {
                type: "pie",
                data: {
                    labels: data.categorias,
                    datasets: [{
                        data: data.valores,
                        backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50", "#9966FF", "#8A2BE2"]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        })
        .catch(error => console.error("Erro ao carregar os dados:", error));
});
