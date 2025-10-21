<?php
// Defina este array no topo do seu script
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>

<form method="GET">
    <select class="seletor-mes" name="m" id="m"
            onchange="document.getElementById('container-loading').classList.remove('hidden'); this.form.submit()">
        <?php foreach ($meses as $numero => $nome): ?>
            <option value="<?= $numero; ?>" <?= ($numero == $m) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($nome); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>