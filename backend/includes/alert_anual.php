<?php
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}
?>

<link rel="stylesheet" href="../assets/css/alert_anual.css">

<div class="overlay-alert" onclick="fecharAlert()">
    <div id="alert-anual">
        <h1>Atenção!</h1>
        <p>
            Todas as informações dos anos anteriores serão deletadas no próximo ano. Você pode gerar um relatório em PDF
            com
            os dados do ano atual.
        </p>
        <div class="btn-container">
            <button id="btn-fechar-alert" onclick="fecharAlert()">Cancelar</button>
            <a href="<?php BASE_URL ?>relatorios/relatorio_anual.php?rel_anual=true" target="_blank">Gerar Relatório</a>
        </div>
    </div>
</div>

<script>
    const overlayAlert = document.querySelector('.overlay-alert');
    const alertAnual = document.getElementById('alert-anual');
    const btnFecharAlert = document.getElementById('btn-fechar-alert');

    function mostrarAlert() {
        alertAnual.style.display = "flex";
        overlayAlert.style.display = "flex";
    }

    function fecharAlert() {
        alertAnual.style.display = "none";
        overlayAlert.style.display = "none";
    }
</script>