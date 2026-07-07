<?php
/**
 * Recordes do mês
 * Variáveis: $dados_recordes
 */
$rec = $dados_recordes;
?>
<div class="card">
    <h3><i class="bi bi-fire"></i> Recordes do Mês</h3>
    <div class="recordes-list">
        <?php if ($rec['maior_despesa']): ?>
            <div class="recorde-item">
                <div class="recorde-icon bg-red-100 text-red-500">
                    <i class="bi bi-arrow-down-circle-fill"></i>
                </div>
                <div class="recorde-info">
                    <span class="recorde-label">Maior despesa</span>
                    <span class="recorde-desc"><?= htmlspecialchars($rec['maior_despesa']['descricao']) ?></span>
                    <span class="recorde-valor text-red-500"><?= formatarReais($rec['maior_despesa']['valor']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($rec['maior_renda']): ?>
            <div class="recorde-item">
                <div class="recorde-icon bg-emerald-100 text-emerald-500">
                    <i class="bi bi-arrow-up-circle-fill"></i>
                </div>
                <div class="recorde-info">
                    <span class="recorde-label">Maior renda</span>
                    <span class="recorde-desc"><?= htmlspecialchars($rec['maior_renda']['descricao']) ?></span>
                    <span class="recorde-valor text-emerald-500"><?= formatarReais($rec['maior_renda']['valor']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($rec['maior_dia']): ?>
            <div class="recorde-item">
                <div class="recorde-icon bg-amber-100 text-amber-500">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div class="recorde-info">
                    <span class="recorde-label">Maior gasto em um dia</span>
                    <span class="recorde-desc"><?= formatarData($rec['maior_dia']['data']) ?></span>
                    <span class="recorde-valor text-amber-500"><?= formatarReais($rec['maior_dia']['total']) ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!$rec['maior_despesa'] && !$rec['maior_renda']): ?>
            <p class="text-texto-opaco text-sm text-center py-5">Sem movimentações</p>
        <?php endif; ?>
    </div>
</div>
