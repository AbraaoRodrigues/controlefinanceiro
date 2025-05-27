function carregarIMask(callback) {
    if (typeof IMask === "undefined") {
        var script = document.createElement("script");
        script.src = "https://unpkg.com/imask";
        script.onload = function () {
            
            if (callback) callback();
        };
        document.head.appendChild(script);
    } else {
        if (callback) callback();
    }
}

function aplicarMascaraNosCampos() {
    carregarIMask(() => {
        let valorInputs = document.querySelectorAll(".campo-valor");

        if (valorInputs.length > 0) {
            valorInputs.forEach(valorInput => {
                IMask(valorInput, {
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
            });
        } else {
            console.warn("Nenhum elemento com a classe .campo-valor encontrado!");
        }
    });
}
