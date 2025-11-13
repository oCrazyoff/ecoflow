<div class="card assistente-ia">
    <h3><i class="bi bi-stars"></i> Assistente I.A</h3>
    <?php if (isset($txt_ia) && !empty(trim($txt_ia))): ?>
        <h4 class="titulo"><?= htmlspecialchars($titulo_ia) ?></h4>
        <p><?= htmlspecialchars($txt_ia) ?></p>
    <?php else: ?>
        <img class="h-20" src="assets/img/esperar.svg" alt="Desenho de chuva">
    <?php endif; ?>
</div>