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

            <div class="graficos">
                <!-- Gráfico Financeiro -->
                <div class="card" id="grafico">
                    <h3>Rendas | Despesas | Investimentos</h3>
                    <canvas id="graficoFinanceiro"></canvas>
                </div>

                <!-- Gráfico de Pizza da Meta -->
                <div class="card">
                    <h3>Meta de Investimentos</h3>
                    <canvas id="graficoProgresso"></canvas>
                </div>

                <!-- Imagem -->
                <div class="card" id="imagem">
                    <img src="../assets/img/dashboard_img.jpg" alt="Eco Flow">
                </div>
            </div>
        </div>
    </div>

    <script>
        // Substitua esses valores pelos dados reais puxados do banco
        let rendaAtiva = 6000;
        let totalDespesas = 1500;
        let totalInvestimentos = 10000;

        let metaInvestimento = rendaAtiva * 6; // Meta de investimento = 6x renda ativa
        let faltaInvestir = metaInvestimento - totalInvestimentos; // Quanto falta para atingir a meta

        // Garante que o valor não fique negativo (caso já tenha ultrapassado a meta)
        faltaInvestir = faltaInvestir < 0 ? 0 : faltaInvestir;

        // Gráfico de Barras
        let ctx1 = document.getElementById('graficoFinanceiro').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Renda', 'Despesas', 'Investimentos'],
                datasets: [{
                    label: 'Valores em R$',
                    data: [rendaAtiva + 500, totalDespesas, totalInvestimentos],
                    backgroundColor: ['green', 'red', 'blue']
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

        // Gráfico de Pizza (Progresso da Meta)
        let ctx2 = document.getElementById('graficoProgresso').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut', // Tipo "doughnut" para ficar mais bonito
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
    </script>

</body>

</html>