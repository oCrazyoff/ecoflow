<aside class="menu">
    <div class="topo-menu">
        <div class="topo-logo">
            <h1 class="logo text-verde">EcoFlow</h1>
            <button id="toggle-menu-btn"><i class="bi bi-chevron-left"></i></button>
        </div>
        <nav>
            <a class="<?= ($rota === 'dashboard') ? 'atual' : '' ?>" href="dashboard">
                <i class="bi bi-columns-gap"></i>
                <span>Dashboard</span>
            </a>
            <a class="<?= ($rota === 'rendas') ? 'atual' : '' ?>" href="rendas">
                <i class="bi bi-cash-stack"></i>
                <span>Rendas</span>
            </a>
            <a class="<?= ($rota === 'despesas') ? 'atual' : '' ?>" href="despesas">
                <i class="bi bi-wallet"></i>
                <span>Despesas</span>
            </a>
        </nav>
    </div>
    <div class="baixo-menu">
        <a class="<?= ($rota === 'perfil') ? 'atual' : '' ?>" href="perfil">
            <i class="bi bi-person-circle"></i>
            <span><?= htmlspecialchars($_SESSION['nome'] ?? 'Usuario') ?></span>
        </a>
    </div>
</aside>