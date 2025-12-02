// Tenta adicionar a classe logo no início para evitar "flash" de conteúdo
document.documentElement.classList.add("loading");

document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("container-loading");

    // Se o loader não existir na página, não faz nada
    if (!loader) return;

    // Função para esconder o loader
    const hideLoader = () => {
        loader.classList.add("hidden");
        document.documentElement.classList.remove("loading");
    };

    // Função para mostrar o loader
    const showLoader = () => {
        loader.classList.remove("hidden");
        document.documentElement.classList.add("loading");
    };

    // 1. Esconde o loader quando a página terminar de carregar
    window.addEventListener("load", hideLoader);

    // 2. Garante que o loader suma se o usuário usar o botão "Voltar"
    window.addEventListener("pageshow", (event) => {
        // Se a página foi carregada do cache (botão voltar)
        if (event.persisted) {
            hideLoader();
        }
    });

    // 3. Mostra o loader antes da página ser recarregada
    window.addEventListener("beforeunload", (e) => {
        // Se o form teve erro, NÃO mostra o loader
        if (window.formHasError === true) {
            e.preventDefault();
            hideLoader();
            window.formHasError = false;
            return;
        }

        showLoader();
    });

    // 4. Mostra o loader ao clicar em links internos
    document.querySelectorAll("a[href]").forEach(link => {
        link.addEventListener("click", e => {
            const href = link.getAttribute("href");

            // Ignora links de âncora (#), links para nova aba ou links vazios
            if (!href || href.startsWith("#") || link.target === "_blank") {
                return;
            }

            showLoader();
        });
    });

    // 5. Mostra o loader ao enviar formulários
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", () => {
            // Se houve erro no formulário, NÃO mostra o loader
            if (window.formHasError === true) {
                hideLoader();
                window.formHasError = false; // reseta
                return;
            }

            showLoader();
        });
    });
});