<div class="card assistente-ia">
    <h3><i class="bi bi-stars animate-pulse"></i> Assistente I.A</h3>
    <?php if (isset($txt_ia) && !empty(trim($txt_ia))): ?>
        <h4 class="titulo"><?= htmlspecialchars($titulo_ia) ?></h4>
        <p><?= htmlspecialchars($txt_ia) ?></p>
    <?php else: ?>
        <img class="bg-verde/20 rounded-2xl py-1 h-25" src="assets/img/esperar.svg" alt="Desenho de espera">
    <?php endif; ?>
</div>