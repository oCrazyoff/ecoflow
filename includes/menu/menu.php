<aside class="menu">
    <div class="topo-menu">
        <div class="topo-logo hidden lg:flex">
            <h1 class="logo text-verde">EcoFlow</h1>
            <button id="toggle-menu-btn"><i class="bi bi-chevron-left"></i></button>
        </div>
        <nav>
            <?php require_once 'link_menu.php'; ?>
        </nav>
    </div>
    <div class="baixo-menu">
        <a class="<?= ($rota === 'perfil') ? 'atual' : '' ?>" href="perfil<?= (isset($m) ? '?m=' . $m : '') ?>">
            <i class="bi bi-person-circle"></i>
            <span><?= htmlspecialchars(explode(' ', $_SESSION['nome'])[0]) ?></span>
        </a>
    </div>
</aside>

<!--menu mobile-->
<div class="menu-mobile">
    <nav>
        <a class="<?= ($rota === 'dashboard') ? 'atual' : '' ?>" href="dashboard<?= (isset($m) ? '?m=' . $m : '') ?>">
            <i class="bi bi-columns-gap"></i>
            <span>Dashboard</span>
        </a>
        <a class="<?= ($rota === 'rendas') ? 'atual' : '' ?>" href="rendas<?= (isset($m) ? '?m=' . $m : '') ?>">
            <i class="bi bi-cash-stack"></i>
            <span>Rendas</span>
        </a>
        <a class="<?= ($rota === 'despesas') ? 'atual' : '' ?>" href="despesas<?= (isset($m) ? '?m=' . $m : '') ?>">
            <i class="bi bi-wallet"></i>
            <span>Despesas</span>
        </a>
        <a class="<?= ($rota === 'categorias') ? 'atual' : '' ?>" href="categorias<?= (isset($m) ? '?m=' . $m : '') ?>">
            <i class="bi bi-tags"></i>
            <span>Categorias</span>
        </a>
        <?php if ($_SESSION['cargo'] == 1): ?>
            <a class="<?= ($rota === 'mais') ? 'atual' : '' ?>" href="mais<?= (isset($m) ? '?m=' . $m : '') ?>">
                <i class="bi bi-list"></i>
                <span>Mais</span>
            </a>
        <?php else: ?>
            <a class="<?= ($rota === 'perfil') ? 'atual' : '' ?>" href="perfil<?= (isset($m) ? '?m=' . $m : '') ?>">
                <i class="bi bi-person"></i>
                <span>Perfil</span>
            </a>
        <?php endif ?>
    </nav>
</div>