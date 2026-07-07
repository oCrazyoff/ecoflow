<?php
/**
 * Indicadores Rápidos
 * Variáveis: $dados_indicadores
 */
$ind = $dados_indicadores;
$items = [
    ['icon' => 'bi-tag-fill', 'label' => 'Maior categoria', 'valor' => $ind['maior_categoria'], 'cor' => 'text-violet-500 bg-violet-100'],
    ['icon' => 'bi-cart-fill', 'label' => 'Maior compra', 'valor' => $ind['maior_compra'], 'cor' => 'text-blue-500 bg-blue-100'],
    ['icon' => 'bi-calendar-date-fill', 'label' => 'Dia que mais gastou', 'valor' => $ind['dia_mais_gasto'], 'cor' => 'text-amber-500 bg-amber-100'],
    ['icon' => 'bi-list-ol', 'label' => 'Total de lançamentos', 'valor' => $ind['total_lancamentos'], 'cor' => 'text-emerald-500 bg-emerald-100'],
    ['icon' => 'bi-calculator-fill', 'label' => 'Despesa média', 'valor' => formatarReais($ind['despesa_media']), 'cor' => 'text-red-500 bg-red-100'],
];
?>
<div class="card">
    <h3><i class="bi bi-pin-angle-fill"></i> Indicadores Rápidos</h3>
    <div class="indicadores-grid">
        <?php foreach ($items as $item): ?>
            <div class="indicador-item">
                <span class="indicador-icon <?= $item['cor'] ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                </span>
                <div class="indicador-info">
                    <span class="indicador-label"><?= $item['label'] ?></span>
                    <span class="indicador-valor"><?= htmlspecialchars($item['valor']) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
