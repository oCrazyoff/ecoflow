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
        <a href="javascript:void(0)" onclick="toggleMenuGaveta()">
            <i class="bi bi-list"></i>
            <span>Mais</span>
        </a>
    </nav>
</div>

<!-- Drawer / Gaveta -->
<div id="menu-gaveta-overlay" class="fixed inset-0 bg-black/50 z-[201] hidden opacity-0 transition-opacity duration-300" onclick="toggleMenuGaveta()"></div>
<div id="menu-gaveta" class="fixed bottom-0 left-0 w-full bg-white rounded-t-3xl z-[202] transform translate-y-full transition-transform duration-300 shadow-[0_-5px_20px_rgba(0,0,0,0.1)]">
    <div class="p-6">
        <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-6"></div>
        
        <div class="grid grid-cols-3 gap-y-6 gap-x-2 text-center mb-8">
            <a href="relatorios" class="flex flex-col items-center gap-2 text-gray-700 hover:text-verde">
                <div class="w-12 h-12 rounded-full bg-verde/10 flex items-center justify-center text-verde text-xl">
                    <i class="bi bi-journal-text"></i>
                </div>
                <span class="text-sm font-medium">Relatórios</span>
            </a>
            <a href="categorias<?= (isset($m) ? '?m=' . $m : '') ?>" class="flex flex-col items-center gap-2 text-gray-700 hover:text-verde">
                <div class="w-12 h-12 rounded-full bg-verde/10 flex items-center justify-center text-verde text-xl">
                    <i class="bi bi-tags"></i>
                </div>
                <span class="text-sm font-medium">Categorias</span>
            </a>
            
            <?php if ($_SESSION['cargo'] == 1): ?>
                <a href="usuarios<?= (isset($m) ? '?m=' . $m : '') ?>" class="flex flex-col items-center gap-2 text-gray-700 hover:text-verde">
                    <div class="w-12 h-12 rounded-full bg-verde/10 flex items-center justify-center text-verde text-xl">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="text-sm font-medium">Usuários</span>
                </a>
                <a href="avisos<?= (isset($m) ? '?m=' . $m : '') ?>" class="flex flex-col items-center gap-2 text-gray-700 hover:text-verde">
                    <div class="w-12 h-12 rounded-full bg-verde/10 flex items-center justify-center text-verde text-xl">
                        <i class="bi bi-bell"></i>
                    </div>
                    <span class="text-sm font-medium">Avisos</span>
                </a>
            <?php endif; ?>
        </div>

        <div class="flex flex-col gap-1 border-t border-gray-100 pt-4">
            <a href="perfil<?= (isset($m) ? '?m=' . $m : '') ?>" class="flex items-center gap-3 p-3 text-verde hover:bg-verde/5 rounded-xl transition-colors font-medium">
                <i class="bi bi-gear text-xl"></i>
                <span>Configurações</span>
            </a>
            <a href="deslogar" class="flex items-center gap-3 p-3 text-red-500 hover:bg-red-50 rounded-xl transition-colors font-medium">
                <i class="bi bi-box-arrow-right text-xl"></i>
                <span>Sair</span>
            </a>
        </div>
    </div>
</div>