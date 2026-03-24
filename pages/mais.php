<?php
$titulo = "Mais Opções";
require_once "includes/layout/inicio.php";
?>
<main class="p-3 py-5 lg:p-10 pb-20 lg:pb-10">
    <h2 class="text-2xl font-bold">Mais Opções</h2>
    <div class="container-mais-opt">
        <?php switch ($_SESSION['cargo']):
            case 0: ?>
                <a href="dashboard<?= (isset($m) ? '?m=' . $m : '') ?>">
                    <div class="container-txt">
                        <i class="bi bi-columns-gap"></i>
                        <div class="txt">
                            <h2>Voltar</h2>
                            <p>Voltar para a dashboard</p>
                        </div>
                    </div>
                    <div class="seta">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
                <?php break; ?>
            <?php
            case 1: ?>
                <a href="perfil<?= (isset($m) ? '?m=' . $m : '') ?>">
                    <div class="container-txt">
                        <i class="bi bi-person"></i>
                        <div class="txt">
                            <h2>Perfil</h2>
                            <p>Visualize suas informações e as edite</p>
                        </div>
                    </div>
                    <div class="seta">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
                <a href="avisos<?= (isset($m) ? '?m=' . $m : '') ?>">
                    <div class="container-txt">
                        <i class="bi bi-bell"></i>
                        <div class="txt">
                            <h2>Avisos</h2>
                            <p>Gerencie os avisos do sistema</p>
                        </div>
                    </div>
                    <div class="seta">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
                <a href="usuarios<?= (isset($m) ? '?m=' . $m : '') ?>">
                    <div class="container-txt">
                        <i class="bi bi-people"></i>
                        <div class="txt">
                            <h2>Usuários</h2>
                            <p>Gerencie os usuários do sistema</p>
                        </div>
                    </div>
                    <div class="seta">
                        <i class="bi bi-chevron-right"></i>
                    </div>
                </a>
                <?php break ?>
        <?php endswitch ?>
    </div>
</main>
<?php require_once "includes/layout/fim.php" ?>