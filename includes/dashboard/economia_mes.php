<?php
/**
 * Economia do Mês — card principal com breakdown
 * Variáveis: $dados_comparacao
 */
$rendas = $dados_comparacao['rendas'];
$despesas = $dados_comparacao['despesas'];
$economia = $rendas - $despesas;
$pct = $rendas > 0 ? round(($economia / $rendas) * 100, 1) : 0;
$positivo = $economia >= 0;
$desp_pagas = $dados_comparacao['despesas_pagas'];
$desp_pendentes = $dados_comparacao['despesas_pendentes'];
?>
<div class="card eco-card">
    <h3><i class="bi bi-piggy-bank"></i> Economia do Mês</h3>
    <div class="eco-stats">
        <div class="eco-item">
            <span class="eco-label">Receitas</span>
            <span class="eco-valor eco-receita"><?= formatarReais($rendas) ?></span>
        </div>
        <div class="eco-separator"></div>
        <div class="eco-item">
            <span class="eco-label">Despesas</span>
            <span class="eco-valor eco-despesa"><?= formatarReais($despesas) ?></span>
            <span class="eco-sub">
                <span class="text-emerald-500"><?= formatarReais($desp_pagas) ?> pagas</span>
                <?php if ($desp_pendentes > 0): ?>
                    · <span class="text-amber-500"><?= formatarReais($desp_pendentes) ?> pendentes</span>
                <?php endif; ?>
            </span>
        </div>
        <div class="eco-separator"></div>
        <div class="eco-item eco-resultado">
            <span class="eco-label"><?= $positivo ? 'Você economizou' : 'Você gastou a mais' ?></span>
            <span class="eco-valor <?= $positivo ? 'eco-positivo' : 'eco-negativo' ?>">
                <?= formatarReais(abs($economia)) ?>
            </span>
            <div class="eco-barra">
                <div class="eco-barra-fill <?= $positivo ? 'eco-fill-positivo' : 'eco-fill-negativo' ?>"
                     style="width: <?= min(abs($pct), 100) ?>%">
                </div>
            </div>
            <span class="eco-pct"><?= abs($pct) ?>% da sua renda</span>
        </div>
    </div>
</div>
