<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
date_default_timezone_set('America/Sao_Paulo');
$rota = $_GET['url'] ?? ''; // rota atual

// verificando se precisa incluir o valida ou não
if (isset($n_valida) && $n_valida == true) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    require_once __DIR__ . "/../../backend/conexao.php";
    require_once __DIR__ . "/../../backend/auth/auto_login.php";
    
    if (isset($_SESSION["id"]) && in_array($rota, ['', 'login', 'cadastro'])) {
        header("Location: " . BASE_URL . "dashboard");
        exit;
    }
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

    <!--CHART JS - somente no dashboard-->
    <?php if ($rota === 'dashboard'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>

    <title><?= htmlspecialchars((isset($titulo) ? $titulo . " • EcoFlow" : 'EcoFlow')) ?></title>

    <?php
    // Determina se esta rota precisa de skeleton
    $temSkeleton = in_array($rota, ['login', 'cadastro', 'dashboard', 'perfil', 'rendas', 'despesas', 'categorias', 'relatorios', 'usuarios', 'avisos', 'mais']);
    if ($temSkeleton): ?>
    <style>
        /* Esconde o conteúdo real até o skeleton sumir */
        body > main, body > nav, body > aside { opacity: 0; }
    </style>
    <?php endif; ?>
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

    // Incluindo o esqueleto baseado na rota
    if ($rota === 'login' || $rota === 'cadastro') {
        require_once __DIR__ . "/../skeletons/auth.php";
    } elseif ($rota === 'dashboard') {
        require_once __DIR__ . "/../skeletons/dashboard.php";
    } elseif ($rota === 'perfil') {
        require_once __DIR__ . "/../skeletons/perfil.php";
    } elseif (in_array($rota, ['rendas', 'despesas', 'categorias', 'relatorios', 'usuarios', 'avisos', 'mais'])) {
        require_once __DIR__ . "/../skeletons/table.php";
    }
    ?>