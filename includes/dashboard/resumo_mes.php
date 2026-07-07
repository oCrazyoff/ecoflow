<?php
/**
 * Resumo do Mês — "Relatório executivo"
 * Variáveis: $dados_comparacao, $dados_indicadores, $dados_categorias, $MESES_NOMES
 */
$mes = dashGetMes();
$ano = date('Y');
$rendas = $dados_comparacao['rendas'];
$despesas = $dados_comparacao['despesas'];
$economia = $rendas - $despesas;
$positivo = $economia >= 0;

// Maior categoria
$top_cat = !empty($dados_categorias['categorias']) ? $dados_categorias['categorias'][0] : null;
$top_cat_nome = $top_cat ? $top_cat['nome'] : '—';
$top_cat_pct = $top_cat ? $top_cat['percentual'] : 0;
?>
<div class="card resumo-mes-card">
    <h3><i class="bi bi-journal-text"></i> Resumo do Mês</h3>
    <h4 class="resumo-titulo"><?= $MESES_NOMES[$mes] ?> de <?= $ano ?></h4>
    <div class="resumo-grid">
        <div class="resumo-item">
            <span class="resumo-label">Receitas</span>
            <span class="resumo-valor text-emerald-500"><?= formatarReais($rendas) ?></span>
        </div>
        <div class="resumo-item">
            <span class="resumo-label">Despesas</span>
            <span class="resumo-valor text-red-500"><?= formatarReais($despesas) ?></span>
        </div>
        <div class="resumo-item resumo-destaque <?= $positivo ? 'resumo-positivo' : 'resumo-negativo' ?>">
            <span class="resumo-label"><?= $positivo ? 'Economizou' : 'Déficit' ?></span>
            <span class="resumo-valor"><?= formatarReais(abs($economia)) ?></span>
        </div>
        <div class="resumo-item">
            <span class="resumo-label">Categoria que mais consumiu</span>
            <span class="resumo-valor"><?= htmlspecialchars($top_cat_nome) ?> (<?= $top_cat_pct ?>%)</span>
        </div>
        <div class="resumo-item">
            <span class="resumo-label">Maior compra</span>
            <span class="resumo-valor"><?= htmlspecialchars($dados_indicadores['maior_compra']) ?></span>
        </div>
        <div class="resumo-item">
            <span class="resumo-label">Dia com maior gasto</span>
            <span class="resumo-valor"><?= $dados_indicadores['dia_mais_gasto'] ?></span>
        </div>
        <div class="resumo-item">
            <span class="resumo-label">Total de movimentações</span>
            <span class="resumo-valor"><?= $dados_indicadores['total_lancamentos'] ?></span>
        </div>
    </div>
</div>
