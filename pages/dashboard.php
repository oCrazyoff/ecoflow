<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Dashboard</title>
    <link rel="stylesheet" href="../frontend/css/dashboard.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="header">
            <h2>Saldo Atual: R$ 5.000,00</h2>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Resumo Financeiro</h3>
                <p><strong>Renda Total:</strong> R$ 6.500,00</p>
                <p><strong>Despesas Totais:</strong> R$ 1.500,00</p>
            </div>

            <div class="card">
                <h3>Despesas Obrigatórias</h3>
                <p>Aluguel: R$ 1.000,00</p>
                <p>Energia: R$ 200,00</p>
                <p>Internet: R$ 100,00</p>
            </div>

            <div class="card">
                <h3>Despesas Não Obrigatórias</h3>
                <p>Streaming: R$ 50,00</p>
                <p>Restaurante: R$ 150,00</p>
            </div>

            <div class="card">
                <h3>Rendas</h3>
                <p><strong>Ativa:</strong> R$ 6.000,00</p>
                <p><strong>Passiva:</strong> R$ 500,00</p>
            </div>

            <div class="card">
                <h3>Investimentos</h3>
                <p>Total Investido: R$ 10.000,00</p>
                <p>Meta: 6x do salário (R$ 36.000,00)</p>
            </div>
        </div>
    </div>
</body>

</html>