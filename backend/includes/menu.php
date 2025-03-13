<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}
?>
<link rel="stylesheet" href="<?php echo BASE_URL ?>frontend/css/menu.css?v=<?php echo time(); ?>">
<div class="sidebar">
    <div class="top">
        <div class="titulo">
            <img src="<?php echo BASE_URL ?>frontend/img/logo.png" alt="Logo Eco Flow">
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
        <div class="user">
            <i class="bi bi-person-circle"></i>
            <p><?= isset($_SESSION['nome']) ? $_SESSION['nome'] : "Usuario" ?></p>
        </div>
    </div>
</div>