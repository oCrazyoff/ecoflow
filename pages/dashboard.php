<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$user_id = $_SESSION['id'];

$sqlRendaTotal = "SELECT valor FROM rendas WHERE user_id = $user_id";
$resultadoRendaTotal = $conn->query($sqlRendaTotal);
$rendaTotal = 0;
if ($resultadoRendaTotal->num_rows > 0) {
    while ($row = $resultadoRendaTotal->fetch_assoc()) {
        $rendaTotal += $row['valor'];
    }
}

$sqlDespesasTotal = "SELECT valor FROM despesas WHERE user_id = $user_id";
$resultadoDespesasTotal = $conn->query($sqlDespesasTotal);
$despesasTotal = 0;
if ($resultadoDespesasTotal->num_rows > 0) {
    while ($row = $resultadoDespesasTotal->fetch_assoc()) {
        $despesasTotal += $row['valor'];
    }
}

$sqlDesesasObrigatorias = "SELECT valor FROM despesas WHERE user_id = $user_id AND tipo = 'Obrigatória'";
$resultadoDespesasObrigatorias = $conn->query($sqlDesesasObrigatorias);
$despesasObrigatorias = 0;
if ($resultadoDespesasObrigatorias->num_rows > 0) {
    while ($row = $resultadoDespesasObrigatorias->fetch_assoc()) {
        $despesasObrigatorias += $row['valor'];
    }
}

$sqlDesesasNaoObrigatorias = "SELECT valor FROM despesas WHERE user_id = $user_id AND tipo = 'Não Obrigatória'";
$resultadoDespesasNaoObrigatorias = $conn->query($sqlDesesasNaoObrigatorias);
$despesasNaoObrigatorias = 0;
if ($resultadoDespesasNaoObrigatorias->num_rows > 0) {
    while ($row = $resultadoDespesasNaoObrigatorias->fetch_assoc()) {
        $despesasNaoObrigatorias += $row['valor'];
    }
}

$sqlRendaAtiva = "SELECT valor FROM rendas WHERE user_id = $user_id AND tipo = 'Ativo'";
$resultadoRendaAtiva = $conn->query($sqlRendaAtiva);
$rendaAtiva = 0;
if ($resultadoRendaAtiva->num_rows > 0) {
    while ($row = $resultadoRendaAtiva->fetch_assoc()) {
        $rendaAtiva += $row['valor'];
    }
}

$sqlRendaPassiva = "SELECT valor FROM rendas WHERE user_id = $user_id AND tipo = 'Passivo'";
$resultadoRendaPassiva = $conn->query($sqlRendaPassiva);
$rendaPassiva = 0;
if ($resultadoRendaPassiva->num_rows > 0) {
    while ($row = $resultadoRendaPassiva->fetch_assoc()) {
        $rendaPassiva += $row['valor'];
    }
}

?>

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
            <h2>Saldo Atual: R$ <?php echo $rendaTotal - $despesasTotal ?></h2>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Resumo Financeiro</h3>
                <p><strong>Renda Total:</strong> R$ <?php echo $rendaTotal ?></p>
                <p><strong>Despesas Totais:</strong> R$ <?php echo $despesasTotal ?></p>
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
                <p><strong>Ativa:</strong> R$ <?php echo $rendaAtiva ?></p>
                <p><strong>Passiva:</strong> R$ <?php echo $rendaPassiva ?></p>
            </div>

            <div class="card">
                <h3>Investimentos</h3>
                <p>Total Investido: R$ 10.000,00</p>
                <p>Meta: 6x do salário (R$ 36.000,00)</p>
            </div>

            <div class="card" id="grafico-coluna">
                <h3>Rendas • Despesas • Investimentos</h3>
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
        let rendaTotal = <?php echo $rendaTotal ?>;
        let despesasObrigatorias = <?php echo $despesasObrigatorias ?>;
        let despesasNaoObrigatorias = <?php echo $despesasNaoObrigatorias ?>;
        let totalDespesas = <?php echo $despesasTotal ?>;
        let investimentos = rendaTotal - totalDespesas;
        if (investimentos < 0) investimentos = 0;

        let ctx1 = document.getElementById('graficoFinanceiro').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Rendas', 'Despesas', 'Investimentos'],
                datasets: [{
                    label: 'Valores em R$',
                    data: [rendaTotal, totalDespesas, investimentos],
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
        let rendaAtiva = <?php echo $rendaAtiva ?>;
        let metaInvestimento = rendaAtiva * 6;
        let totalInvestimentos = <?php echo $rendaTotal * 0.5 ?>;
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