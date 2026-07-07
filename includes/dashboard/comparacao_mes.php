<?php
/**
 * Comparação com o mês anterior — 4 mini-cards
 * Variáveis: $dados_comparacao
 */
$items = [
    [
        'label' => 'Receitas',
        'valor' => $dados_comparacao['rendas'],
        'var' => $dados_comparacao['var_rendas'],
        'icon' => 'bi-cash-stack',
        'positivo_bom' => true,
        'cor' => 'emerald'
    ],
    [
        'label' => 'Despesas',
        'valor' => $dados_comparacao['despesas'],
        'var' => $dados_comparacao['var_despesas'],
        'icon' => 'bi-graph-down-arrow',
        'positivo_bom' => false, // para despesas, subir é ruim
        'cor' => 'red'
    ],
    [
        'label' => 'Saldo',
        'valor' => $dados_comparacao['saldo'],
        'var' => $dados_comparacao['var_saldo'],
        'icon' => 'bi-piggy-bank',
        'positivo_bom' => true,
        'cor' => 'blue'
    ],
    [
        'label' => 'Economia',
        'valor' => null,
        'valor_fmt' => $dados_comparacao['economia_pct'] . '%',
        'var' => $dados_comparacao['var_economia'],
        'icon' => 'bi-shield-check',
        'positivo_bom' => true,
        'cor' => 'violet'
    ],
];
?>
<section class="comparacao-section">
    <?php foreach ($items as $i => $item):
        $variacao = $item['var'];
        // Para despesas, lógica é invertida
        if ($item['positivo_bom']) {
            $is_bom = $variacao >= 0;
        } else {
            $is_bom = $variacao <= 0;
        }
        $seta = $variacao >= 0 ? '▲' : '▼';
        $cor_var = $is_bom ? 'text-emerald-500' : 'text-red-500';
        $bg_var = $is_bom ? 'bg-emerald-50' : 'bg-red-50';
        $valor_exibir = isset($item['valor_fmt']) ? $item['valor_fmt'] : formatarReais($item['valor']);
    ?>
        <div class="comp-card" style="animation-delay: <?= $i * 80 ?>ms">
            <div class="comp-header">
                <span class="comp-label"><?= $item['label'] ?></span>
                <div class="comp-icon comp-icon-<?= $item['cor'] ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                </div>
            </div>
            <span class="comp-valor"><?= $valor_exibir ?></span>
            <?php if ($variacao != 0): ?>
                <span class="comp-variacao <?= $cor_var ?> <?= $bg_var ?>">
                    <?= $seta ?> <?= $variacao > 0 ? '+' : '' ?><?= $variacao ?>%
                </span>
            <?php else: ?>
                <span class="comp-variacao bg-gray-100 text-gray-500">— sem variação</span>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>
