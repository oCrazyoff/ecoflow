<?php
date_default_timezone_set('America/Sao_Paulo');
$rota = $_GET['url'] ?? ''; // rota atual

// verificando se precisa incluir o valida ou não
if (isset($n_valida) && $n_valida == true) {
    session_start();
    require_once __DIR__ . "/../../backend/conexao.php";
} else {
    require_once __DIR__ . "/../../backend/valida.php";
}

// pegando o valor mes da URL
if (!isset($_GET['m']) || $_GET['m'] < 0 || $_GET['m'] > 13) {
    $m = date('n');
    $_SESSION['m'] = $m;
} else {
    $m = $_GET['m'];
    $_SESSION['m'] = $m;
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="<?= BASE_URL ?>assets/css/output.css?v=<?= time() ?>" rel="stylesheet">
    <link rel="shortcut icon" href="<?= BASE_URL . "assets/img/logo.png" ?>" type="image/x-icon">

    <!--CHART JS-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <title><?= htmlspecialchars((isset($titulo) ? $titulo . " • EcoFlow" : 'EcoFlow')) ?></title>
</head>

<body <?= ((isset($n_valida) && $n_valida == true) || $rota == 'relatorio') ? "class='flex-col h-auto'" : "" ?>>
    <?php
    // removendo menu e aviso das paginas sem rota e proibidas
    if (array_key_exists($rota, $routes)) {
        if (isset($_SESSION['id']) && $rota !== '' && $rota !== 'login' && $rota !== 'cadastro' && $rota !== 'relatorio') {
            include __DIR__ . "/../menu/menu.php";
            require_once __DIR__ . "/../aviso.php";
        }
    }

    // incluindo o loading e o aviso
    require_once __DIR__ . "/../loading.php";
    ?>