<?php if (totalRendas() > 0 || despesasPagas() > 0 || despesasPendentes() > 0): ?>
<div class="card assistente-ia">
    <h3><i class="bi bi-stars"></i> Assistente I.A</h3>
    <?php if (!empty(trim($txt_ia))): ?>
    <h4 class="titulo"><?= htmlspecialchars($titulo_ia) ?></h4>
    <p><?= htmlspecialchars($txt_ia) ?></p>
    <?php else: ?>
    <i class="bi bi-exclamation-triangle text-5xl text-verde text-center"></i>
    <?php endif; ?>
</div>
<?php endif; ?>