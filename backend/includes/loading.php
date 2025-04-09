<div id="loading-screen">
    <div class="spinner"></div>
</div>
<script>
// Ocultar o loading screen quando a página terminar de carregar
window.addEventListener('load', () => {
    const loadingScreen = document.getElementById('loading-screen');
    loadingScreen.style.display = 'none';
});

// Ao clicar em links o loading aparece novamente
document.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        const loadingScreen = document.getElementById('loading-screen');
        loadingScreen.style.display = 'block';
    });
});
</script>