<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}
?>
<link rel="stylesheet" href="<?php echo BASE_URL ?>assets/css/menu.css?v=<?php echo time(); ?>">
<div class="overlay"></div>
<div id="header-mobile">
    <div class="btn-menu"><i class="bi bi-list"></i></div>
    <img src="<?php echo BASE_URL ?>assets/img/logo.png" alt="Logo Eco Flow">
</div>
<aside class="sidebar">
    <div class="top">
        <div class="header">
            <h3>EcoFlow</h3>
            <button id="btn-menu" onclick="toggleSidebar()"><i class="bi bi-chevron-left"></i></button>
        </div>
        <nav>
            <a href="<?php echo BASE_URL ?>pages/dashboard.php"><i class="bi-grid"></i>
                <p>Dashboard</p>
            </a>
            <a href="<?php echo BASE_URL ?>pages/rendas.php"><i class="bi bi-graph-up-arrow"></i>
                <p>Rendas</p>
            </a>
            <a href="<?php echo BASE_URL ?>pages/despesas.php"><i class="bi bi-cash-stack"></i>
                <p>Despesas</p>
            </a>
            <a href="<?php echo BASE_URL ?>pages/investimentos.php"><i class="bi bi-bank"></i>
                <p>Investimentos</p>
            </a>
        </nav>
    </div>
    <div class="bottom">
        <a href="<?php echo BASE_URL ?>pages/user_config.php">
            <i class="bi bi-person-circle"></i>
            <p><?= isset($_SESSION['nome']) ? $_SESSION['nome'] : "Usuario" ?></p>
        </a>
    </div>
</aside>

<script>
    // Verificar a URL para marcar item do menu
    document.addEventListener('DOMContentLoaded', () => {
        const currentPath = window.location.pathname.replace(/\/+$/, '');

        document.querySelectorAll('nav a').forEach(link => {
            const linkPath = new URL(link.href).pathname.replace(/\/+$/, '');
            if (linkPath === currentPath) {
                link.classList.add('atual');
            }
        });
    });

    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.btn-menu').classList.toggle('active');
        document.querySelector('.overlay').classList.toggle('active');
    }

    document.querySelector('.btn-menu').addEventListener('click', () => {
        toggleSidebar();
    });

    document.querySelector('.overlay').addEventListener('click', () => {
        toggleSidebar();
    });

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
            }, 100);
        });
    });
</script>