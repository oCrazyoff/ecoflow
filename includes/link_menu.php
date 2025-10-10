<a class="<?= ($rota === 'dashboard') ? 'atual' : '' ?>"
   href="dashboard<?= (isset($m) ? '?m=' . $m : '') ?>">
    <i class="bi bi-columns-gap"></i>
    <span>Dashboard</span>
</a>
<a class="<?= ($rota === 'rendas') ? 'atual' : '' ?>"
   href="rendas<?= (isset($m) ? '?m=' . $m : '') ?>">
    <i class="bi bi-cash-stack"></i>
    <span>Rendas</span>
</a>
<a class="<?= ($rota === 'despesas') ? 'atual' : '' ?>"
   href="despesas<?= (isset($m) ? '?m=' . $m : '') ?>">
    <i class="bi bi-wallet"></i>
    <span>Despesas</span>
</a>