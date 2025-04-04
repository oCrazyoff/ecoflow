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

$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n') - 1; // Mês atual por padrão

// Atualizar consultas com base no mês selecionado
$rendaTotal = calcularTotal($conn, "SELECT valor FROM rendas WHERE user_id = $user_id AND MONTH(data) = $selectedMonth + 1");
$despesasTotal = calcularTotal($conn, "SELECT valor FROM despesas WHERE user_id = $user_id AND MONTH(data) = $selectedMonth + 1");
$despesasObrigatorias = calcularTotal($conn, "SELECT valor FROM despesas WHERE user_id = $user_id AND tipo = 'Obrigatória' AND MONTH(data) = $selectedMonth + 1");
$despesasNaoObrigatorias = calcularTotal($conn, "SELECT valor FROM despesas WHERE user_id = $user_id AND tipo = 'Não Obrigatória' AND MONTH(data) = $selectedMonth + 1");
$rendaAtiva = calcularTotal($conn, "SELECT valor FROM rendas WHERE user_id = $user_id AND tipo = 'Ativo' AND MONTH(data) = $selectedMonth + 1");
$rendaPassiva = calcularTotal($conn, "SELECT valor FROM rendas WHERE user_id = $user_id AND tipo = 'Passivo' AND MONTH(data) = $selectedMonth + 1");

// Meta de investimento
$metaInvestimento = $rendaAtiva * 6;

// Consultar investimentos com base no mês selecionado
$investimentos = [];
$sqlInvestimentos = "SELECT nome, custo, tipo FROM investimentos WHERE user_id = $user_id AND MONTH(data) = $selectedMonth + 1 LIMIT 4";
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
                <h3>Despesas Obrigatórias</h3>
                <?php
                $sql = "SELECT descricao, valor FROM despesas WHERE user_id = $user_id AND tipo = 'Obrigatória' AND MONTH(data) = $selectedMonth + 1 LIMIT 4";
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
                $sql = "SELECT descricao, valor FROM despesas WHERE user_id = $user_id AND tipo = 'Não Obrigatória' AND MONTH(data) = $selectedMonth + 1 LIMIT 4";
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
    monthButton.innerHTML = `<i class="bi bi-caret-down-fill"></i> ${monthNames[selectedMonth]}`;

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
            url.searchParams.set('month', selectedMonth);
            window.location.href = url.toString();
        });
    });
    </script>
</body>

</html>