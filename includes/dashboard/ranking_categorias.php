<?php
/**
 * Ranking de Categorias com barras horizontais
 * Variáveis: $dados_categorias, $CORES_CATEGORIAS
 */
$categorias = $dados_categorias['categorias'];
$medalhas = ['🥇', '🥈', '🥉'];
$max_valor = !empty($categorias) ? (float)$categorias[0]['total'] : 1;
?>
<div class="card">
    <h3><i class="bi bi-trophy"></i> Ranking de Categorias</h3>
    <?php if (!empty($categorias)): ?>
        <div class="ranking-list">
            <?php foreach (array_slice($categorias, 0, 5) as $i => $cat):
                $bar_width = ($max_valor > 0) ? round(($cat['total'] / $max_valor) * 100) : 0;
                $cor = $CORES_CATEGORIAS[$i % count($CORES_CATEGORIAS)];
            ?>
                <div class="ranking-item" style="animation-delay: <?= $i * 100 ?>ms">
                    <div class="ranking-pos">
                        <?= $i < 3 ? $medalhas[$i] : ($i + 1) . 'º' ?>
                    </div>
                    <div class="ranking-info">
                        <div class="ranking-header">
                            <span class="ranking-nome"><?= htmlspecialchars($cat['nome']) ?></span>
                            <span class="ranking-valores">
                                <?= formatarReais($cat['total']) ?>
                                <span class="ranking-pct">(<?= $cat['percentual'] ?>%)</span>
                            </span>
                        </div>
                        <div class="ranking-bar">
                            <div class="ranking-bar-fill" style="--bar-width: <?= $bar_width ?>%; background: <?= $cor ?>"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-texto-opaco text-sm text-center py-5">Nenhuma despesa categorizada</p>
    <?php endif; ?>
</div>
