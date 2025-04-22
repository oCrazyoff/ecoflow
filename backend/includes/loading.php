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