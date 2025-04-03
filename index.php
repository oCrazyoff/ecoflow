<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow</title>
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>">
    <?php include("backend/includes/head.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="main-content">
        <header>
            <div class="left">
                <img src="<?php echo BASE_URL ?>assets/img/logo.png" alt="Logo Eco Flow">
            </div>
            <div class="right">
                <a id="btn-login" href="<?php echo BASE_URL ?>login.php">Login</a>
                <a id="btn-cadastro" href="<?php echo BASE_URL ?>cadastro.php">Cadastro</a>
            </div>
        </header>
        <div id="hero">
            <div class="left">
                <h1>Vem ser <br><span>Eco Flow</span></h1>
                <p>
                    Gerencie seus gastos, acompanhe suas rendas e automatize seus investimentos em um só lugar. O
                    EcoFlow simplifica seu planejamento financeiro para que você tenha mais controle e liberdade.
                </p>
                <a id="btn-cadastro" href="<?php echo BASE_URL ?>cadastro.php">Abrir conta</a>
            </div>
            <div class="right">
                <img src="<?php echo BASE_URL ?>assets/img/index_img.png" alt="Hero">
            </div>
        </div>
    </div>
</body>

</html>