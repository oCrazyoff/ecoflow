/**
 * Skeleton Loader - Remove o esqueleto e revela o conteúdo real.
 * Permite também reexibir o skeleton durante a navegação pelo menu ou seletor de mês.
 */
(function () {
    const skeleton = document.getElementById("skeleton-loader");

    // Função global para reexibir o skeleton quando houver navegação
    window.mostrarSkeleton = function () {
        const sk = document.getElementById("skeleton-loader");
        const main = document.querySelector("main");
        if (sk) {
            sk.style.display = "flex";
            sk.style.pointerEvents = "all";
            void sk.offsetHeight; // Força reflow para aplicar transição
            sk.style.transition = "opacity 0.15s ease";
            sk.style.opacity = "1";
        }
        if (main) {
            main.style.transition = "opacity 0.15s ease";
            main.style.opacity = "0";
        }
    };

    if (skeleton) {
        const main = document.querySelector("main");
        const nav = document.querySelector("nav");
        const aside = document.querySelector("aside");

        // Fade out inicial do skeleton
        skeleton.style.transition = "opacity 0.15s ease";
        skeleton.style.opacity = "0";

        // Após a transição, esconde o esqueleto (mantendo no DOM) e revela o conteúdo
        setTimeout(() => {
            skeleton.style.display = "none";

            [main, nav, aside].forEach(el => {
                if (el) {
                    el.style.transition = "opacity 0.15s ease";
                    el.style.opacity = "1";
                }
            });
        }, 150);
    }

    // Configurar ouvintes de eventos para navegação (menu e seletor de mês)
    const initSkeletonEvents = () => {
        // Links do menu
        const menuLinks = document.querySelectorAll("aside.menu a, .menu-mobile a, #menu-gaveta a, .container-mais-opt a");
        menuLinks.forEach(link => {
            link.addEventListener("click", function (e) {
                const href = this.getAttribute("href");
                if (href && !href.startsWith("javascript:") && href !== "#" && !this.target && !e.ctrlKey && !e.metaKey && !e.shiftKey && e.button === 0) {
                    if (typeof window.mostrarSkeleton === "function") {
                        window.mostrarSkeleton();
                    }
                }
            });
        });

        // Seletor de mês
        const seletoresMes = document.querySelectorAll(".seletor-mes");
        seletoresMes.forEach(select => {
            select.addEventListener("change", function () {
                if (typeof window.mostrarSkeleton === "function") {
                    window.mostrarSkeleton();
                }
            });
        });
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initSkeletonEvents);
    } else {
        initSkeletonEvents();
    }
})();
