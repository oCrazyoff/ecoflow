<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}
?>
<link rel="stylesheet" href="<?php echo BASE_URL ?>frontend/css/menu.css">
<div class="sidebar">
    <div class="titulo">
        <img src="<?php echo BASE_URL ?>frontend/img/logo.png" alt="Logo Eco Flow">
        <h1>Eco Flow</h1>
    </div>
    <a href="<?php echo BASE_URL ?>index.php"><i class="bi bi-house"></i> Inicio</a>
    <a href="<?php echo BASE_URL ?>pages/despesas.php"><i class="bi bi-cash-stack"></i> Despesas</a>
    <a href="<?php echo BASE_URL ?>pages/rendas.php"><i class="bi bi-graph-up-arrow"></i> Rendas</a>
    <a href="<?php echo BASE_URL ?>pages/investimentos.php"><i class="bi bi-bank"></i> Investimentos</a>
    <a href="<?php echo BASE_URL ?>pages/config.php"><i class="bi bi-gear"></i> Configurações</a>
</div>