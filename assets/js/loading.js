document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("container-loading");

    // Esconde o loader quando a página terminar de carregar
    window.addEventListener("load", () => {
        if (loader) loader.classList.add("hidden");
    });

    // Mostra o loader antes de sair da página (links)
    document.querySelectorAll("a[href]").forEach(link => {
        link.addEventListener("click", e => {
            const href = link.getAttribute("href");

            // Evita ativar em âncoras vazias ou links externos
            if (!href || href.startsWith("#") || link.target === "_blank") return;

            loader.classList.remove("hidden");
        });
    });

    // Mostra o loader em qualquer envio de formulário
    document.querySelectorAll("form").forEach(form => {
        form.addEventListener("submit", () => {
            loader.classList.remove("hidden");
        });
    });
});