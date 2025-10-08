<aside class="menu">
    <div class="topo-menu">
        <div class="topo-logo">
            <h1 class="logo text-verde">EcoFlow</h1>
            <button><i class="bi bi-chevron-left"></i></button>
        </div>
        <nav>
            <a class="<?= ($rota === 'dashboard') ? 'atual' : '' ?>" href="dashboard">
                <i class="bi bi-columns-gap"></i>
                Dashboard
            </a>
            <a class="<?= ($rota === 'rendas') ? 'atual' : '' ?>" href="rendas">
                <i class="bi bi-cash-stack"></i>
                Rendas
            </a>
            <a class="<?= ($rota === 'despesas') ? 'atual' : '' ?>" href="despesas">
                <i class="bi bi-wallet"></i>
                Despesas
            </a>
        </nav>
    </div>
    <div class="baixo-menu">
        <a class="<?= ($rota === 'perfil') ? 'atual' : '' ?>" href="perfil">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuario') ?>
        </a>
    </div>
</aside>