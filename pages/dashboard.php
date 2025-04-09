<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$user_id = $_SESSION['id'];
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Consulta única para rendas
$sqlRendas = "SELECT descricao, valor FROM rendas WHERE user_id = $user_id AND (MONTH(data) = $selectedMonth OR recorrente = 'Sim') LIMIT 4";
$rendaResult = $conn->query($sqlRendas);

$rendas = [];
$rendaTotal = 0;

if ($rendaResult->num_rows > 0) {
    while ($row = $rendaResult->fetch_assoc()) {
        $rendas[] = $row;
        $rendaTotal += $row['valor'];
    }
}

// Consulta única para despesas
$sqlDespesas = "
    SELECT 
        SUM(valor) AS total,
        SUM(CASE WHEN status = 'Pago' THEN valor ELSE 0 END) AS pagas,
        SUM(CASE WHEN status = 'Não Pago' THEN valor ELSE 0 END) AS nao_pagas
    FROM despesas
    WHERE user_id = $user_id AND (MONTH(data) = $selectedMonth OR recorrente = 'Sim')
";
$despesasResult = $conn->query($sqlDespesas)->fetch_assoc();
$despesasTotal = $despesasResult['total'] ?? 0;
$despesasPagas = $despesasResult['pagas'] ?? 0;
$despesasNaoPagas = $despesasResult['nao_pagas'] ?? 0;

// Consulta de investimentos (limite 4)
$investimentos = [];
$sqlInvestimentos = "SELECT nome, custo, tipo FROM investimentos WHERE user_id = $user_id AND (MONTH(data) = $selectedMonth OR recorrente = 'Sim') LIMIT 4";
$resultadoInvestimentos = $conn->query($sqlInvestimentos);
if ($resultadoInvestimentos->num_rows > 0) {
    while ($row = $resultadoInvestimentos->fetch_assoc()) {
        $investimentos[] = $row;
    }
}

// Cálculo de distribuição de investimentos
$totalInvestimentos = 0;
$investimentosFixo = $investimentosAcao = $investimentosFII = 0;

foreach ($investimentos as $inv) {
    $totalInvestimentos += $inv['custo'];
    switch ($inv['tipo']) {
        case 'Renda Fixa':
            $investimentosFixo += $inv['custo'];
            break;
        case 'Ação':
            $investimentosAcao += $inv['custo'];
            break;
        case 'FII':
            $investimentosFII += $inv['custo'];
            break;
    }
}

// Porcentagens dos gráficos
$totalDistribuicao = $rendaTotal + $despesasPagas + $despesasNaoPagas;
$pctRendas = $totalDistribuicao > 0 ? ($rendaTotal / $totalDistribuicao) * 100 : 0;
$pctDespesasPagas = $totalDistribuicao > 0 ? ($despesasPagas / $totalDistribuicao) * 100 : 0;
$pctDespesasNaoPagas = $totalDistribuicao > 0 ? ($despesasNaoPagas / $totalDistribuicao) * 100 : 0;

$pctFixo = $totalInvestimentos > 0 ? ($investimentosFixo / $totalInvestimentos) * 100 : 0;
$pctAcao = $totalInvestimentos > 0 ? ($investimentosAcao / $totalInvestimentos) * 100 : 0;
$pctFII = $totalInvestimentos > 0 ? ($investimentosFII / $totalInvestimentos) * 100 : 0;

// Obter despesas não pagas diretamente do banco de dados
$despesasNaoPagas = [];
$sqlDespesasNaoPagas = "SELECT descricao AS nome, valor FROM despesas WHERE user_id = $user_id AND status = 'Não Pago' AND (MONTH(data) = $selectedMonth OR recorrente = 'Sim')";
$resultadoDespesasNaoPagas = $conn->query($sqlDespesasNaoPagas);
if ($resultadoDespesasNaoPagas->num_rows > 0) {
    while ($row = $resultadoDespesasNaoPagas->fetch_assoc()) {
        $despesasNaoPagas[] = $row;
    }
}

// Calcular o total de despesas não pagas
$totalNaoPago = array_sum(array_column($despesasNaoPagas, 'valor'));
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
    <?php include("../backend/includes/loading.php") ?>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="header">
            <h2>Saldo: R$ <?php echo number_format($rendaTotal - $despesasTotal, 2, ',', '.') ?></h2>
            <div class="data-container">
                <button id="monthButton"><i class="bi bi-caret-down-fill"></i> </button>
                <ul id="monthList" style="display: none;">
                    <li data-month="0">Janeiro</li>
                    <li data-month="1">Fevereiro</li>
                    <li data-month="2">Março</li>
                    <li data-month="3">Abril</li>
                    <li data-month="4">Maio</li>
                    <li data-month="5">Junho</li>
                    <li data-month="6">Julho</li>
                    <li data-month="7">Agosto</li>
                    <li data-month="8">Setembro</li>
                    <li data-month="9">Outubro</li>
                    <li data-month="10">Novembro</li>
                    <li data-month="11">Dezembro</li>
                </ul>
            </div>
        </div>

        <div class="cards">
            <div class="card">
                <h3>Resumo Financeiro</h3>
                <p><strong>Renda Total:</strong> R$ <?php echo number_format($rendaTotal, 2, ',', '.') ?></p>
                <p><strong>Despesas Totais:</strong> R$ <?php echo number_format($despesasTotal, 2, ',', '.') ?></p>
                <p><strong>Investimentos Totais:</strong> R$
                    <?php echo number_format($totalInvestimentos, 2, ',', '.') ?></p>
            </div>

            <div class="card">
                <h3>Despesas Pagas <a href="despesas.php"><i class="bi bi-arrow-up-right-square-fill"></i></a></h3>
                <?php
                $sql = "SELECT descricao, valor FROM despesas WHERE user_id = $user_id AND status = 'Pago' AND (MONTH(data) = $selectedMonth OR recorrente = 'Sim') LIMIT 4";
                $resultado = $conn->query($sql);
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<p><strong>" . $row['descricao'] . ":</strong> R$ " . number_format($row['valor'], 2, ',', '.') . "</p>";
                    }
                } else {
                    echo "<p style='text-align:center;'>Nenhuma despesa paga❌</p>";
                }
                ?>
            </div>

            <div class="card">
                <h3>Despesas Não Pagas <a href="despesas.php"><i class="bi bi-arrow-up-right-square-fill"></i></a></h3>
                <?php
                $sql = "SELECT descricao, valor FROM despesas WHERE user_id = $user_id AND status = 'Não Pago' AND (MONTH(data) = $selectedMonth OR recorrente = 'Sim') LIMIT 4";
                $resultado = $conn->query($sql);
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<p><strong>" . $row['descricao'] . ":</strong> R$ " . number_format($row['valor'], 2, ',', '.') . "</p>";
                    }
                } else {
                    echo "<p style='text-align:center;'>Todas despesas pagas✅</p>";
                }
                ?>
            </div>

            <div class="card">
                <h3>Rendas <a href="rendas.php"><i class="bi bi-arrow-up-right-square-fill"></i></a></h3>
                <?php if (empty($rendas)): ?>
                    <p style="text-align:center;">Nenhuma renda❌</p>
                <?php else: ?>
                    <?php foreach ($rendas as $renda): ?>
                        <p><strong><?php echo $renda['descricao']; ?>:</strong> R$
                            <?php echo number_format($renda['valor'], 2, ',', '.'); ?></p>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>Investimentos <a href="investimentos.php"><i class="bi bi-arrow-up-right-square-fill"></i></a></h3>
                <?php
                foreach ($investimentos as $investimento) {
                    echo "<p><strong>" . $investimento['nome'] . ":</strong> R$ " . number_format($investimento['custo'], 2, ',', '.') . "</p>";
                }
                ?>
                </p>
            </div>

            <div class="card" id="grafico-coluna">
                <h3>Rendas • Despesas • Investimentos</h3>
                <canvas id="graficoFinanceiro"></canvas>
            </div>

            <div class="card" id="grafico">
                <h3>Despesas Não Pagas</h3>
                <canvas id="graficoDespesasNaoPagas" style="max-width: 400px;"></canvas>
                <p id="mensagemDespesasPagas" style="text-align:center; display:none;">
                    Todas as despesas estão pagas ✅
                </p>
            </div>

            <div class="card" id="grafico">
                <h3>Distribuição Financeira</h3>
                <canvas id="graficoDistribuicao"></canvas>
            </div>
        </div>
    </div>

    <script>
        const phpSelectedMonth = <?php echo $selectedMonth; ?>; // Passar o mês selecionado do PHP
    </script>
    <script>
        // Gráfico de Rendas, Despesas e Investimentos
        let ctx1 = document.getElementById('graficoFinanceiro').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Rendas', 'Despesas', 'Investimentos'],
                datasets: [{
                    label: 'Valores em R$',
                    data: [<?php echo $rendaTotal ?>, <?php echo $despesasTotal ?>,
                        <?php echo array_sum(array_column($investimentos, 'custo')) ?>
                    ],
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

        // Despesas não pagas
        const despesasNaoPagas = <?php echo json_encode($despesasNaoPagas); ?>;

        if (despesasNaoPagas.length === 0) {
            document.getElementById('graficoDespesasNaoPagas').style.display = 'none';
            document.getElementById('mensagemDespesasPagas').style.display = 'block';
        } else {
            const labels = despesasNaoPagas.map(d => d.nome);
            const valores = despesasNaoPagas.map(d => Number(d.valor));

            new Chart(document.getElementById('graficoDespesasNaoPagas'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: valores,
                        backgroundColor: [
                            '#e76f51', '#f4a261', '#2a9d8f', '#e9c46a', '#264653', '#a8dadc', '#ffafcc'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const data = context.dataset.data.map(Number); // força números
                                    const total = data.reduce((a, b) => a + b, 0);
                                    const valor = Number(context.parsed);
                                    const pct = ((valor / total) * 100).toFixed(1);
                                    return `${context.label}: R$${valor.toFixed(2)} (${pct}%)`;
                                }

                            }
                        }
                    }
                }
            });
        }

        // Gráfico de Distribuição
        let ctxDistribuicao = document.getElementById('graficoDistribuicao').getContext('2d');
        new Chart(ctxDistribuicao, {
            type: 'pie',
            data: {
                labels: [
                    `Rendas (${<?php echo number_format($pctRendas, 1, '.', ''); ?>}%)`,
                    `Despesas Pagas (${<?php echo number_format($pctDespesasPagas, 1, '.', ''); ?>}%)`,
                    `Despesas Não Pagas (${<?php echo number_format($pctDespesasNaoPagas, 1, '.', ''); ?>}%)`
                ],
                datasets: [{
                    data: [<?php echo $pctRendas; ?>, <?php echo $pctDespesasPagas; ?>,
                        <?php echo $pctDespesasNaoPagas; ?>
                    ],
                    backgroundColor: ['#4caf50', '#f44336', '#ff9800']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });

        // Obter o mês atual
        const monthNames = [
            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
        ];

        // Verificar se o parâmetro 'month' está presente na URL
        const urlParams = new URLSearchParams(window.location.search);
        const selectedMonth = urlParams.has('month') ? parseInt(urlParams.get('month')) : phpSelectedMonth;

        const monthButton = document.getElementById('monthButton');
        const monthList = document.getElementById('monthList');
        const monthItems = monthList.querySelectorAll('li');

        // Exibir o mês selecionado no botão
        monthButton.innerHTML = `<i class="bi bi-caret-down-fill"></i> ${monthNames[phpSelectedMonth - 1]}`;

        // Alternar a exibição da lista de meses ao clicar no botão
        monthButton.addEventListener('click', () => {
            monthList.style.display = monthList.style.display === 'none' ? 'block' : 'none';
        });

        // Fechar a lista ao clicar fora dela
        document.addEventListener('click', (event) => {
            if (!monthButton.contains(event.target) && !monthList.contains(event.target)) {
                monthList.style.display = 'none';
            }
        });

        // Tornar os itens da lista clicáveis e enviar o mês selecionado ao backend
        monthItems.forEach((item) => {
            item.addEventListener('click', () => {
                const selectedMonth = item.getAttribute('data-month');
                monthButton.innerHTML =
                    `<i class="bi bi-caret-down-fill"></i> ${monthNames[selectedMonth]}`;
                monthList.style.display = 'none';

                // Atualizar a página com o mês selecionado
                const url = new URL(window.location.href);
                url.searchParams.set('month', parseInt(selectedMonth) + 1); // Ajuste para PHP
                window.location.href = url.toString();
            });
        });
    </script>
</body>

</html>