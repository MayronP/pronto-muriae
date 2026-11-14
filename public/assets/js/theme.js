/* Interações da interface. */
(function () {
    "use strict";

    // Imprime a página.
    document.querySelectorAll("[data-imprimir]").forEach(function (botao) {
        botao.addEventListener("click", function () {
            window.print();
        });
    });

    // Mostra ou oculta a senha.
    document.querySelectorAll("[data-mostra-senha]").forEach(function (botao) {
        var campo = document.getElementById(botao.getAttribute("data-mostra-senha"));

        botao.addEventListener("click", function () {
            var mostrar = campo.type === "password";

            campo.type = mostrar ? "text" : "password";
            botao.querySelector(".fa").className = mostrar ? "fa fa-eye-slash" : "fa fa-eye";
            botao.setAttribute("aria-label", mostrar ? "Ocultar senha" : "Mostrar senha");
        });
    });

    // Exibe os campos conforme a opção selecionada.
    document.querySelectorAll("[data-mostra-quando]").forEach(function (bloco) {
        var regra = bloco.getAttribute("data-mostra-quando").split("=");
        var valores = regra[1].split(",");
        var form = bloco.closest("form") || document;
        var campos = form.querySelectorAll("[name='" + regra[0] + "']");

        var atualiza = function () {
            var valor = "";

            campos.forEach(function (campo) {
                if (campo.type !== "radio" || campo.checked) {
                    valor = campo.value;
                }
            });

            bloco.classList.toggle("d-none", valores.indexOf(valor) === -1);
        };

        campos.forEach(function (campo) {
            campo.addEventListener("change", atualiza);
        });

        atualiza();
    });

    // Busca o código CID-10 e mostra a descrição.
    document.querySelectorAll("[data-busca-cid]").forEach(function (campo) {
        var lista = document.getElementById(campo.getAttribute("list"));
        var descricao = document.querySelector(campo.getAttribute("data-cid-descricao"));
        var espera;
        var requisicao;
        var aviso = document.createElement("small");
        aviso.className = "d-block text-muted mt-1";
        aviso.id = (campo.id || "cid") + "-busca-status";
        aviso.setAttribute("role", "status");
        campo.insertAdjacentElement("afterend", aviso);
        campo.setAttribute("aria-describedby", ((campo.getAttribute("aria-describedby") || "") + " " + aviso.id).trim());

        if (!lista) return;

        campo.addEventListener("input", function () {
            var termo = campo.value.trim();

            lista.querySelectorAll("option").forEach(function (opcao) {
                if (descricao && opcao.value === termo) {
                    descricao.textContent = opcao.label;
                }
            });

            clearTimeout(espera);
            if (requisicao) requisicao.abort();
            aviso.textContent = "";

            if (termo.length < 2) {
                lista.replaceChildren();
                return;
            }

            espera = setTimeout(function () {
                requisicao = new AbortController();
                lista.replaceChildren();
                aviso.textContent = "Buscando códigos…";
                fetch(campo.getAttribute("data-busca-cid") + "?termo=" + encodeURIComponent(termo), { credentials: "same-origin", signal: requisicao.signal })
                    .then(function (resposta) {
                        if (resposta.redirected || !resposta.ok || !(resposta.headers.get("content-type") || "").includes("application/json")) {
                            throw new Error("Busca indisponível");
                        }
                        return resposta.json();
                    })
                    .then(function (cids) {
                        if (campo.value.trim() !== termo) return;
                        if (!Array.isArray(cids)) throw new Error("Resposta inválida");
                        lista.innerHTML = "";

                        cids.forEach(function (cid) {
                            var opcao = document.createElement("option");

                            opcao.value = cid.codigo;
                            opcao.label = cid.descricao;
                            lista.appendChild(opcao);
                        });
                        aviso.textContent = cids.length ? "" : "Nenhum código encontrado. Revise a busca.";
                    })
                    .catch(function (erro) {
                        if (erro.name === "AbortError" || campo.value.trim() !== termo) return;
                        lista.replaceChildren();
                        aviso.textContent = "Não foi possível buscar os códigos. Confira sua conexão e sessão e tente novamente.";
                    });
            }, 250);
        });
    });
})();
