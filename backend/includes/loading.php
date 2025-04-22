<div id="loading-screen">
    <div class="spinner"></div>
</div>
<script>
    // Ocultar o loading screen quando a página terminar de carregar
    window.addEventListener('load', () => {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.style.display = 'none';
    });
</script>

<script defer>
    // Ao clicar em links, exibir o loading novamente
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', (event) => {
            const href = link.getAttribute('href');

            // Ignorar links sem destino ou com atributos especiais
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
                return;
            }

            event.preventDefault(); // Previne a navegação imediata
            const loadingScreen = document.getElementById('loading-screen');
            loadingScreen.style.display = 'flex';

            // Aguarda um curto período antes de redirecionar
            setTimeout(() => {
                window.location.href = href;
            }, 300);
        });
    });
</script>