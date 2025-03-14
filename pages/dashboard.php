<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
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

            <div class="card" id="grafico-coluna">
                <h3>Rendas | Despesas | Investimentos</h3>
                <canvas id="graficoFinanceiro"></canvas>
            </div>

            <div class="card" id="grafico">
                <h3>Progresso da Meta de Investimentos</h3>
                <canvas id="graficoProgresso"></canvas>
            </div>

            <div class="card" id="grafico">
                <h3>Distribuição das Despesas e Rendimento</h3>
                <canvas id="graficoDistribuicao"></canvas>
            </div>
        </div>
    </div>

    <script>
        let rendaTotal = 6500;
        let despesasObrigatorias = 1300;
        let despesasNaoObrigatorias = 200;
        let totalDespesas = despesasObrigatorias + despesasNaoObrigatorias;
        let investimentos = rendaTotal - totalDespesas;
        if (investimentos < 0) investimentos = 0;

        let ctx1 = document.getElementById('graficoFinanceiro').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Renda', 'Despesas', 'Investimentos'],
                datasets: [{
                    label: 'Valores em R$',
                    data: [rendaTotal, totalDespesas, 10000],
                    backgroundColor: ['#4c956c', '#d90429', '#219ebc']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        let ctx2 = document.getElementById('graficoProgresso').getContext('2d');
        let rendaAtiva = 6000;
        let metaInvestimento = rendaAtiva * 6;
        let totalInvestimentos = 10000;
        let faltaInvestir = metaInvestimento - totalInvestimentos;
        faltaInvestir = faltaInvestir < 0 ? 0 : faltaInvestir;

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Já Investido', 'Falta Investir'],
                datasets: [{
                    data: [totalInvestimentos, faltaInvestir],
                    backgroundColor: ['#3498db', '#e0e0e0']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        let totalDistribuicao = despesasObrigatorias + despesasNaoObrigatorias + investimentos;
        let pctObrigatorias = (despesasObrigatorias / totalDistribuicao) * 100;
        let pctNaoObrigatorias = (despesasNaoObrigatorias / totalDistribuicao) * 100;
        let pctInvestimentos = (investimentos / totalDistribuicao) * 100;

        let ctxDistribuicao = document.getElementById('graficoDistribuicao').getContext('2d');
        new Chart(ctxDistribuicao, {
            type: 'pie',
            data: {
                labels: [
                    `Obrigatórias (${pctObrigatorias.toFixed(1)}%)`,
                    `Não Obrigatórias (${pctNaoObrigatorias.toFixed(1)}%)`,
                    `Investimentos (${pctInvestimentos.toFixed(1)}%)`
                ],
                datasets: [{
                    data: [pctObrigatorias, pctNaoObrigatorias, pctInvestimentos],
                    backgroundColor: ['#d90429', '#fb8500', '#4c956c']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>

</html>