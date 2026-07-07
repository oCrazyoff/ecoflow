<?php
/**
 * Gasto por semana do mês
 * Variáveis: $dados_semana
 */
$max_semana = max(array_column($dados_semana, 'total'));
$total_semanas = array_sum(array_column($dados_semana, 'total'));
?>
<div class="card">
    <h3><i class="bi bi-calendar-week"></i> Gasto por Semana</h3>
    <?php if ($total_semanas > 0): ?>
        <div class="semana-list">
            <?php foreach ($dados_semana as $i => $sem):
                $bar_width = ($max_semana > 0) ? round(($sem['total'] / $max_semana) * 100) : 0;
                $is_max = ($sem['total'] == $max_semana && $sem['total'] > 0);
            ?>
                <div class="semana-item <?= $is_max ? 'semana-destaque' : '' ?>">
                    <div class="semana-header">
                        <div>
                            <span class="semana-label"><?= $sem['label'] ?></span>
                            <span class="semana-periodo">dias <?= $sem['periodo'] ?></span>
                        </div>
                        <span class="semana-valor"><?= formatarReais($sem['total']) ?></span>
                    </div>
                    <div class="semana-bar">
                        <div class="semana-bar-fill <?= $is_max ? 'semana-fill-max' : '' ?>"
                             style="--bar-width: <?= $bar_width ?>%"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-texto-opaco text-sm text-center py-5">Nenhuma despesa registrada</p>
    <?php endif; ?>
</div>
