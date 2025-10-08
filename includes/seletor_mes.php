<?php
// Defina este array no topo do seu script
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Lógica para saber qual mês está ativo
$mes_selecionado = filter_input(INPUT_GET, 'm', FILTER_VALIDATE_INT) ?? date('n');
?>

<form method="GET">
    <select class="seletor-mes" name="m" id="m" onchange="this.form.submit()">
        <?php foreach ($meses as $numero => $nome): ?>
            <option value="<?= $numero; ?>" <?= ($numero == $mes_selecionado) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($nome); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>