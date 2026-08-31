/**
 * Skeleton Loader - Remove o esqueleto e revela o conteúdo real.
 * Permite também reexibir o skeleton durante a navegação pelo menu ou seletor de mês.
 */
(function () {
    const skeleton = document.getElementById("skeleton-loader");

    // Função para esconder o skeleton e revelar o conteúdo
    function esconderSkeleton() {
        const sk = document.getElementById("skeleton-loader");
        const main = document.querySelector("main");
        const nav = document.querySelector("nav");
        const aside = document.querySelector("aside");

        if (sk) {
            sk.style.transition = "opacity 0.15s ease";
            sk.style.opacity = "0";

            setTimeout(() => {
                sk.style.display = "none";
                sk.style.pointerEvents = "none";

                [main, nav, aside].forEach(el => {
                    if (el) {
                        el.style.transition = "opacity 0.15s ease";
                        el.style.opacity = "1";
                    }
                });
            }, 150);
        }
    }

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
        // Fade out inicial do skeleton
        esconderSkeleton();

        // Timeout de segurança: se por algum motivo o skeleton não foi removido, forçar
        setTimeout(() => {
            const sk = document.getElementById("skeleton-loader");
            if (sk && sk.style.display !== "none") {
                esconderSkeleton();
            }
        }, 15000);
    }

    // Fix para BFCache (Back/Forward Cache)
    // Quando o usuário volta com o botão do navegador, destravar o skeleton
    window.addEventListener("pageshow", function (event) {
        if (event.persisted) {
            esconderSkeleton();
        }
    });

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

        // Seletor de mês — NÃO adicionar listener aqui pois o onchange inline já chama mostrarSkeleton
        // Isso evita o disparo duplicado
    };

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initSkeletonEvents);
    } else {
        initSkeletonEvents();
    }
})();
