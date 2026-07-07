<?php
/**
 * Distribuição dos gastos — barra stacked + legenda
 * Variáveis: $dados_categorias, $CORES_CATEGORIAS
 */
$categorias = $dados_categorias['categorias'];
$total = $dados_categorias['total'];
?>
<div class="card">
    <h3><i class="bi bi-bar-chart-fill"></i> Distribuição dos Gastos</h3>
    <?php if (!empty($categorias) && $total > 0): ?>
        <div class="distrib-bar">
            <?php foreach ($categorias as $i => $cat):
                $cor = $CORES_CATEGORIAS[$i % count($CORES_CATEGORIAS)];
                $largura = $cat['percentual'];
            ?>
                <div class="distrib-segment"
                     style="width: <?= $largura ?>%; background: <?= $cor ?>"
                     title="<?= htmlspecialchars($cat['nome']) ?> — <?= $cat['percentual'] ?>%">
                </div>
            <?php endforeach; ?>
        </div>
        <div class="distrib-legend">
            <?php foreach ($categorias as $i => $cat):
                $cor = $CORES_CATEGORIAS[$i % count($CORES_CATEGORIAS)];
            ?>
                <div class="distrib-item">
                    <span class="distrib-color" style="background: <?= $cor ?>"></span>
                    <span class="distrib-nome"><?= htmlspecialchars($cat['nome']) ?></span>
                    <span class="distrib-info"><?= $cat['percentual'] ?>% · <?= formatarReais($cat['total']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-texto-opaco text-sm text-center py-5">Sem dados de despesas</p>
    <?php endif; ?>
</div>
