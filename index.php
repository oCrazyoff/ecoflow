<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Inicio</title>
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>">
    <?php include("backend/includes/head.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="main-content">
        <header>
            <div class="left">
                <img src="<?php echo BASE_URL ?>assets/img/logo.png" alt="Logo Eco Flow">
                <h1>Eco Flow</h1>
            </div>
            <div class="right">
                <a id="btn-login" href="<?php echo BASE_URL ?>backend/usuario/login.php">Login</a>
                <a id="btn-cadastro" href="<?php echo BASE_URL ?>backend/usuario/cadastrar.php">Cadastro</a>
            </div>
        </header>
        <div id="hero">
            <div class="left">
                <h1>Vem ser <br><span>Eco Flow</span></h1>
                <p>
                    Faça como os melhores investidores do Brasil
                    e invista seu salário com consciência
                    e sabedoria.
                </p>
                <a id="btn-cadastro" href="<?php echo BASE_URL ?>pages/dashboard.php">Abrir conta</a>
            </div>
            <div class="right">
                <img src="<?php echo BASE_URL ?>assets/img/moeda_index.png" alt="Hero">
            </div>
        </div>
    </div>
</body>

</html>