/**
 * Skeleton Loader - Remove o esqueleto e revela o conteúdo real.
 * 
 * Este script é carregado no final do <body> (via fim.php), portanto
 * quando ele executa, todo o DOM já está construído e pronto.
 * Não precisamos esperar window.onload (que espera imagens/fontes externas
 * e causa o flash do skeleton sobre conteúdo já renderizado).
 */
(function () {
    const skeleton = document.getElementById("skeleton-loader");
    if (!skeleton) return;

    // Revela o conteúdo real que estava escondido via inline <style> no <head>
    const main = document.querySelector("main");
    const nav = document.querySelector("nav");
    const aside = document.querySelector("aside");

    // Fade out do skeleton
    skeleton.style.transition = "opacity 0.3s ease";
    skeleton.style.opacity = "0";

    // Após a transição, remove o skeleton e revela o conteúdo
    setTimeout(() => {
        skeleton.remove();

        // Revela o conteúdo com uma transição suave
        [main, nav, aside].forEach(el => {
            if (el) {
                el.style.transition = "opacity 0.3s ease";
                el.style.opacity = "1";
            }
        });
    }, 300);
})();
