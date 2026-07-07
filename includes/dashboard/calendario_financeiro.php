<?php
/**
 * Calendário Financeiro — grade mensal com dots
 * Variáveis: $dados_calendario
 */
$cal = $dados_calendario;
$diasSemana = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
?>
<div class="card">
    <h3><i class="bi bi-calendar3"></i> Calendário Financeiro</h3>
    <div class="cal-legend">
        <span class="cal-legend-item"><span class="cal-dot-leg cal-receita"></span> Receitas</span>
        <span class="cal-legend-item"><span class="cal-dot-leg cal-despesa"></span> Despesas</span>
        <span class="cal-legend-item"><span class="cal-dot-leg cal-parcela"></span> Parcelas</span>
        <span class="cal-legend-item"><span class="cal-dot-leg cal-recorrente"></span> Recorrentes</span>
    </div>
    <div class="cal-grid">
        <?php foreach ($diasSemana as $ds): ?>
            <div class="cal-header"><?= $ds ?></div>
        <?php endforeach; ?>

        <?php // Células vazias antes do primeiro dia
        for ($e = 0; $e < $cal['primeiro_dia']; $e++): ?>
            <div class="cal-empty"></div>
        <?php endfor; ?>

        <?php // Dias do mês
        for ($d = 1; $d <= $cal['num_dias']; $d++):
            $tem_receita = isset($cal['rendas'][$d]);
            $tem_despesa = isset($cal['despesas'][$d]);
            $tem_parcela = in_array($d, $cal['parcelas']);
            $tem_recorrente = in_array($d, $cal['recorrentes']);
            $tem_algo = $tem_receita || $tem_despesa || $tem_parcela || $tem_recorrente;
            $is_hoje = ($d == date('j') && $cal['mes'] == date('n') && $cal['ano'] == date('Y'));
        ?>
            <div class="cal-day <?= $is_hoje ? 'cal-hoje' : '' ?> <?= $tem_algo ? 'cal-ativo' : '' ?>">
                <span class="cal-num"><?= $d ?></span>
                <?php if ($tem_algo): ?>
                    <div class="cal-dots">
                        <?php if ($tem_receita): ?><span class="cal-dot cal-receita" title="Receita: <?= formatarReais($cal['rendas'][$d]) ?>"></span><?php endif; ?>
                        <?php if ($tem_despesa): ?><span class="cal-dot cal-despesa" title="Despesa: <?= formatarReais($cal['despesas'][$d]) ?>"></span><?php endif; ?>
                        <?php if ($tem_parcela): ?><span class="cal-dot cal-parcela" title="Parcela"></span><?php endif; ?>
                        <?php if ($tem_recorrente): ?><span class="cal-dot cal-recorrente" title="Recorrente"></span><?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>
