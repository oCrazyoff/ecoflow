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
<div class="btn-menu"><i class="bi bi-list"></i></div>
<div class="sidebar">
    <div class="top">
        <div class="logo">
            <img src="<?php echo BASE_URL ?>assets/img/logo.png" alt="Logo Eco Flow">
            <h1>Eco Flow</h1>
        </div>
        <a href="<?php echo BASE_URL ?>pages/dashboard.php"><i class="bi-grid"></i>
            <p>Dashboard</p>
        </a>
        <a href="<?php echo BASE_URL ?>pages/despesas.php"><i class="bi bi-cash-stack"></i>
            <p>Despesas</p>
        </a>
        <a href="<?php echo BASE_URL ?>pages/rendas.php"><i class="bi bi-graph-up-arrow"></i>
            <p>Rendas</p>
        </a>
        <a href="<?php echo BASE_URL ?>pages/investimentos.php"><i class="bi bi-bank"></i>
            <p>Investimentos</p>
        </a>
    </div>
    <div class="bottom">
        <a href="<?php echo BASE_URL ?>pages/user_config.php">
            <i class="bi bi-person-circle"></i>
            <p><?= isset($_SESSION['nome']) ? $_SESSION['nome'] : "Usuario" ?></p>
        </a>
    </div>
</div>

<script>
    document.querySelector('.btn-menu').addEventListener('click', () => {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.btn-menu').classList.toggle('active');
        document.querySelector('.btn-menu i').classList.toggle('bi-list');
        document.querySelector('.btn-menu i').classList.toggle('bi-x');
    });
</script>