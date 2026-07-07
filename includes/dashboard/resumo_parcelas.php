<?php
/**
 * Resumo das parcelas
 * Variáveis: $dados_parcelas
 */
$p = $dados_parcelas;
?>
<div class="card">
    <h3><i class="bi bi-layers"></i> Resumo das Parcelas</h3>
    <?php if ($p['parcelas_mes'] > 0 || $p['parcelas_restantes'] > 0): ?>
        <div class="parcelas-grid">
            <div class="parcela-stat">
                <span class="parcela-icon bg-violet-100 text-violet-600"><i class="bi bi-receipt"></i></span>
                <div class="parcela-info">
                    <span class="parcela-num"><?= $p['parcelas_mes'] ?></span>
                    <span class="parcela-label">Parcelas este mês</span>
                </div>
            </div>
            <div class="parcela-stat">
                <span class="parcela-icon bg-blue-100 text-blue-600"><i class="bi bi-cash"></i></span>
                <div class="parcela-info">
                    <span class="parcela-num"><?= formatarReais($p['valor_mes']) ?></span>
                    <span class="parcela-label">Valor mensal</span>
                </div>
            </div>
            <div class="parcela-stat">
                <span class="parcela-icon bg-amber-100 text-amber-600"><i class="bi bi-hourglass-split"></i></span>
                <div class="parcela-info">
                    <span class="parcela-num"><?= $p['parcelas_restantes'] ?></span>
                    <span class="parcela-label">Parcelas restantes</span>
                </div>
            </div>
            <div class="parcela-stat">
                <span class="parcela-icon bg-red-100 text-red-600"><i class="bi bi-exclamation-triangle"></i></span>
                <div class="parcela-info">
                    <span class="parcela-num"><?= formatarReais($p['saldo_restante']) ?></span>
                    <span class="parcela-label">Saldo restante</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center py-5 gap-2">
            <i class="bi bi-check-circle text-3xl text-emerald-400"></i>
            <p class="text-texto-opaco text-sm">Nenhuma parcela ativa</p>
        </div>
    <?php endif; ?>
</div>
