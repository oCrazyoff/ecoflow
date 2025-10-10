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
        <?php require 'link_menu.php'; ?>
        <a class="<?= ($rota === 'perfil') ? 'atual' : '' ?>" href="perfil<?= (isset($m) ? '?m=' . $m : '') ?>">
            <i class="bi bi-person-circle"></i>
            <span>Perfil</span>
        </a>
    </nav>
</div>