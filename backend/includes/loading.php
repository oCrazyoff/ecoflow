<div id="loading-screen">
    <div class="spinner"></div>
</div>
<script>
// Ocultar o loading screen quando a página terminar de carregar
window.addEventListener('load', () => {
    const loadingScreen = document.getElementById('loading-screen');
    loadingScreen.style.display = 'none';
});

// Ao clicar em links, exibir o loading novamente
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', (event) => {
        event.preventDefault(); // Previne a navegação imediata
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.style.display = 'flex';

        // Aguarda um curto período antes de redirecionar
        setTimeout(() => {
            window.location.href = link.href;
        }, 100); // Ajuste o tempo, se necessário
    });
});
</script>