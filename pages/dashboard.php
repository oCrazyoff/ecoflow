<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$user_id = $_SESSION['id'];

// Função para calcular o total de valores com base em uma consulta SQL
function calcularTotal($conn, $sql)
{
    $resultado = $conn->query($sql);
    $total = 0;
    if ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $total += $row['valor'];
        }
    }
    return $total;
}

// Consultas otimizadas
$rendaTotal = calcularTotal($conn, "SELECT valor FROM rendas WHERE user_id = $user_id");
$despesasTotal = calcularTotal($conn, "SELECT valor FROM despesas WHERE user_id = $user_id");
$despesasObrigatorias = calcularTotal($conn, "SELECT valor FROM despesas WHERE user_id = $user_id AND tipo = 'Obrigatória'");
$despesasNaoObrigatorias = calcularTotal($conn, "SELECT valor FROM despesas WHERE user_id = $user_id AND tipo = 'Não Obrigatória'");
$rendaAtiva = calcularTotal($conn, "SELECT valor FROM rendas WHERE user_id = $user_id AND tipo = 'Ativo'");
$rendaPassiva = calcularTotal($conn, "SELECT valor FROM rendas WHERE user_id = $user_id AND tipo = 'Passivo'");

// Meta de investimento
$metaInvestimento = $rendaAtiva * 6;

// Consultar investimentos
$investimentos = [];
$sqlInvestimentos = "SELECT nome, custo, tipo FROM investimentos WHERE user_id = $user_id LIMIT 4";
$resultadoInvestimentos = $conn->query($sqlInvestimentos);
if ($resultadoInvestimentos->num_rows > 0) {
    while ($row = $resultadoInvestimentos->fetch_assoc()) {
        $investimentos[] = $row;
    }
}

// Calcular os tipos de investimentos
$totalInvestimentos = array_sum(array_column($investimentos, 'custo'));

$investimentosFixo = 0;
$investimentosAcao = 0;
$investimentosFII = 0;

foreach ($investimentos as $inv) {
    if ($inv['tipo'] == 'Renda Fixa') {
        $investimentosFixo += $inv['custo'];
    } elseif ($inv['tipo'] == 'Ação') {
        $investimentosAcao += $inv['custo'];
    } elseif ($inv['tipo'] == 'FII') {
        $investimentosFII += $inv['custo'];
    }
}

$pctFixo = ($totalInvestimentos > 0) ? ($investimentosFixo / $totalInvestimentos) * 100 : 0;
$pctAcao = ($totalInvestimentos > 0) ? ($investimentosAcao / $totalInvestimentos) * 100 : 0;
$pctFII = ($totalInvestimentos > 0) ? ($investimentosFII / $totalInvestimentos) * 100 : 0;

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
            <h2>Saldo Atual: R$ <?php echo number_format($rendaTotal - $despesasTotal, 2, ',', '.') ?></h2>
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
                <h3>Despesas Obrigatórias</h3>
                <?php
                $sql = "SELECT descricao, valor FROM despesas WHERE user_id = $user_id AND tipo = 'Obrigatória' LIMIT 4";
                $resultado = $conn->query($sql);
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<p><strong>" . $row['descricao'] . ":</strong> R$ " . number_format($row['valor'], 2, ',', '.') . "</p>";
                    }
                }
                ?>
            </div>

            <div class="card">
                <h3>Despesas Não Obrigatórias</h3>
                <?php
                $sql = "SELECT descricao, valor FROM despesas WHERE user_id = $user_id AND tipo = 'Não Obrigatória' LIMIT 4";
                $resultado = $conn->query($sql);
                if ($resultado->num_rows > 0) {
                    while ($row = $resultado->fetch_assoc()) {
                        echo "<p><strong>" . $row['descricao'] . ":</strong> R$ " . number_format($row['valor'], 2, ',', '.') . "</p>";
                    }
                }
                ?>
            </div>

            <div class="card">
                <h3>Rendas</h3>
                <p><strong>Ativa:</strong> R$ <?php echo number_format($rendaAtiva, 2, ',', '.') ?></p>
                <p><strong>Passiva:</strong> R$ <?php echo number_format($rendaPassiva, 2, ',', '.') ?></p>
            </div>

            <div class="card">
                <h3>Investimentos</h3>
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
                <h3>Meta de Investimentos</h3>
                <canvas id="graficoProgresso"></canvas>
            </div>

            <div class="card" id="grafico">
                <h3>Distribuição dos Investimentos</h3>
                <canvas id="graficoDistribuicao"></canvas>
            </div>
        </div>
    </div>

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

        // Gráfico de Progresso da Meta de Investimentos
        let ctx2 = document.getElementById('graficoProgresso').getContext('2d');
        let totalInvestimentos = <?php echo $totalInvestimentos ?>;
        let faltaInvestir = <?php echo $metaInvestimento ?> - totalInvestimentos;
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

        // Gráfico de Distribuição
        let totalDistribuicao = <?php echo $totalInvestimentos; ?>;
        let pctFixo = <?php echo $pctFixo; ?>;
        let pctAcao = <?php echo $pctAcao; ?>;
        let pctFII = <?php echo $pctFII; ?>;

        let ctxDistribuicao = document.getElementById('graficoDistribuicao').getContext('2d');
        new Chart(ctxDistribuicao, {
            type: 'pie',
            data: {
                labels: [
                    `Renda Fixa (${pctFixo.toFixed(1)}%)`,
                    `Ações (${pctAcao.toFixed(1)}%)`,
                    `Fundos Imboliarios (${pctFII.toFixed(1)}%)`
                ],
                datasets: [{
                    data: [pctFixo, pctAcao, pctFII],
                    backgroundColor: ['#3498db', '#2ecc71', '#e74c3c']
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