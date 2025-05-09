<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$user_id = $_SESSION['id'];
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Verifica se é janeiro para deletar informações do ano anterior
if (date('n') == 1) {
    $anoAnterior = date('Y') - 1;

    // Deletar rendas
    $sqlDeletarRendas = "DELETE FROM rendas WHERE user_id = ? AND YEAR(data) = ?";
    $stmtDeletarRendas = $conn->prepare($sqlDeletarRendas);
    $stmtDeletarRendas->bind_param("ss", $_SESSION['id'], $anoAnterior);
    $stmtDeletarRendas->execute();

    // Deletar despesas
    $sqlDeletarDespesas = "DELETE FROM despesas WHERE user_id = ? AND YEAR(data) = ?";
    $stmtDeletarDespesas = $conn->prepare($sqlDeletarDespesas);
    $stmtDeletarDespesas->bind_param("ss", $_SESSION['id'], $anoAnterior);
    $stmtDeletarDespesas->execute();

    // Deletar investimentos
    $sqlDeletarInvestimentos = "DELETE FROM investimentos WHERE user_id = ? AND YEAR(data) = ?";
    $stmtDeletarInvestimentos = $conn->prepare($sqlDeletarInvestimentos);
    $stmtDeletarInvestimentos->bind_param("ss", $_SESSION['id'], $anoAnterior);
    $stmtDeletarInvestimentos->execute();
}

$mes_anterior = (date('m') == '01' ? 12 : date('m') - 1);
$mes_atual = date('m');
$ano_atual = date('Y');

// Verifica se as rendas ja foram cadastradas
$sql_verificar = "SELECT descricao, valor FROM rendas WHERE user_id = ? AND recorrente = 'Sim' AND MONTH(data) = ? AND YEAR(data) = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("iii", $user_id, $mes_anterior, $ano_atual);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

while ($row = $result_verificar->fetch_assoc()) {
    $descricao = $row['descricao'];

    $sql_verifica_atual = "SELECT id FROM rendas WHERE user_id = ? AND descricao = ? AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt_verificar_atual = $conn->prepare($sql_verifica_atual);;
    $stmt_verificar_atual->bind_param("isii", $user_id, $descricao, $mes_atual, $ano_atual);
    $stmt_verificar_atual->execute();
    $result_verificar_atual = $stmt_verificar_atual->get_result();

    if ($result_verificar_atual->num_rows == 0) {
        // Inserir renda recorrente
        $sql_inserir = "INSERT INTO rendas (user_id, descricao, valor, recorrente) VALUES (?, ?, 0, 'Sim')";
        $stmt_inserir = $conn->prepare($sql_inserir);
        $stmt_inserir->bind_param("is", $user_id, $descricao);
        $stmt_inserir->execute();
    }
}

// Verifica se as despesas ja foram cadastradas
$sql_verificar = "SELECT descricao, valor, status FROM despesas WHERE user_id = ? AND recorrente = 'Sim' AND MONTH(data) = ? AND YEAR(data) = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("iii", $user_id, $mes_anterior, $ano_atual);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

while ($row = $result_verificar->fetch_assoc()) {
    $descricao = $row['descricao'];
    $status = $row['status'];

    $sql_verifica_atual = "SELECT id FROM despesas WHERE user_id = ? AND descricao = ? AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt_verificar_atual = $conn->prepare($sql_verifica_atual);;
    $stmt_verificar_atual->bind_param("isii", $user_id, $descricao, $mes_atual, $ano_atual);
    $stmt_verificar_atual->execute();
    $result_verificar_atual = $stmt_verificar_atual->get_result();

    if ($result_verificar_atual->num_rows == 0) {
        // Inserir despesa recorrente
        $sql_inserir = "INSERT INTO despesas (user_id, descricao, valor, status, recorrente) VALUES (?, ?, 0, 'Não Pago', 'Sim')";
        $stmt_inserir = $conn->prepare($sql_inserir);
        $stmt_inserir->bind_param("is", $user_id, $descricao);
        $stmt_inserir->execute();
    }
}

// Verifica se os invesimentos ja foram cadastrados
$sql_verificar = "SELECT nome, tipo, custo FROM investimentos WHERE user_id = ? AND recorrente = 'Sim' AND MONTH(data) = ? AND YEAR(data) = ?";
$stmt_verificar = $conn->prepare($sql_verificar);
$stmt_verificar->bind_param("iii", $user_id, $mes_anterior, $ano_atual);
$stmt_verificar->execute();
$result_verificar = $stmt_verificar->get_result();

while ($row = $result_verificar->fetch_assoc()) {
    $nome = $row['nome'];
    $tipo = $row['tipo'];
    $custo = $row['custo'];

    $sql_verifica_atual = "SELECT id FROM investimentos WHERE user_id = ? AND nome = ? AND tipo = ? AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt_verificar_atual = $conn->prepare($sql_verifica_atual);;
    $stmt_verificar_atual->bind_param("issii", $user_id, $nome, $tipo, $mes_atual, $ano_atual);
    $stmt_verificar_atual->execute();
    $result_verificar_atual = $stmt_verificar_atual->get_result();

    if ($result_verificar_atual->num_rows == 0) {
        // Inserir investimento recorrente
        $sql_inserir = "INSERT INTO investimentos (user_id, nome, custo, tipo, recorrente) VALUES (?, ?, 0, ?, 'Sim')";
        $stmt_inserir = $conn->prepare($sql_inserir);
        $stmt_inserir->bind_param("iss", $user_id, $nome, $tipo);
        $stmt_inserir->execute();
    }
}

// Renda total
$sql = "SELECT SUM(valor) FROM rendas WHERE user_id = ? AND (MONTH(data) = $selectedMonth)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($renda_total);
$stmt->fetch();
$stmt->close();

// Despesas pagas total
$sql = "SELECT SUM(valor) FROM despesas WHERE status = 'Pago' AND user_id = ? AND (MONTH(data) = $selectedMonth)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($despesas_pagas_total);
$stmt->fetch();
$stmt->close();

// Despesas não pagas total
$sql = "SELECT SUM(valor) FROM despesas WHERE status = 'Não Pago' AND user_id = ? AND (MONTH(data) = $selectedMonth)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($despesas_nao_pagas_total);
$stmt->fetch();
$stmt->close();

// Investimentos total
$sql = "SELECT SUM(custo) FROM investimentos WHERE user_id = ? AND (MONTH(data) = $selectedMonth)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($investimentos_total);
$stmt->fetch();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.cjs.map"></script>
</head>

<body>
    <?php include("../backend/includes/loading.php") ?>
    <?php include("../backend/includes/menu.php") ?>
    <?php include("../backend/includes/alert_anual.php") ?>
    <div class="main-content">
        <div class="header">
            <h2>Saldo: R$ <?php echo number_format($renda_total - $despesas_pagas_total, 2, ',', '.') ?></h2>
            <?php include("../backend/includes/seletor_data.php") ?>
        </div>

        <div class="container-dash">
            <div class="cards">
                <div class="card">
                    <span><i class="bi bi-wallet"></i></span>
                    <h3>Total de Rendas</h3>
                    <p id="valor"><strong>R$ <?php echo number_format($renda_total, 2, ',', '.') ?></strong></p>
                    <p>Neste mês</p>
                </div>

                <div class="card">
                    <span><i class="bi bi-graph-down-arrow"></i></span>
                    <h3>Despesas Pagas</h3>
                    <p id="valor"><strong>R$ <?php echo number_format($despesas_pagas_total, 2, ',', '.') ?></strong>
                    </p>
                    <p>Neste mês</p>
                </div>

                <div class="card" id="pendentes">
                    <span><i class="bi bi-currency-dollar"></i></span>
                    <h3>Despesas Não Pagas</h3>
                    <p id="valor"><strong>R$
                            <?php echo number_format($despesas_nao_pagas_total, 2, ',', '.') ?></strong></p>
                    <p>Para este mês</p>
                </div>

                <div class="card">
                    <span><i class="bi bi-graph-up-arrow"></i></span>
                    <h3>Investimentos</h3>
                    <p id="valor"><strong>R$ <?php echo number_format($investimentos_total, 2, ',', '.') ?></strong></p>
                    <p>Atualizado hoje</p>
                </div>
            </div>
            <div class="container-graficos">
                <div class="container-esquerda">
                    <div class="card" id="grafico-coluna">
                        <h3>Análise Financeira</h3>
                        <canvas id="grafico_analise"></canvas>
                    </div>
                </div>

                <div class="container-direita">
                    <div class="card" id="resumo">
                        <h3>Resumo Financeiro</h3>
                        <!-- Maiores rendas -->
                        <?php
                        $sql_rendas = "SELECT descricao, valor, data FROM rendas WHERE user_id = ? AND (MONTH(data) = $selectedMonth) ORDER BY valor DESC LIMIT 3";
                        $stmt_rendas = $conn->prepare($sql_rendas);
                        $stmt_rendas->bind_param("i", $user_id);
                        $stmt_rendas->execute();
                        $result_rendas = $stmt_rendas->get_result();

                        if ($result_rendas->num_rows > 0) {
                            echo "<div class='container-resumo'>";
                            echo "<h4>Maiores Rendas</h4>";

                            while ($row = $result_rendas->fetch_assoc()) {
                                echo "<article>";
                                echo "<div class='info'>";
                                echo $row['descricao'];
                                echo "<br>";
                                echo "<p>" . date("d/m/Y", strtotime($row['data'])) . "</p>";
                                echo "</div>";
                                echo "<p class='verde'>R$ " . number_format($row['valor'], 2, ',', '.') . "</p>";
                                echo "</article>";
                            }
                            echo "</div>";
                        }
                        ?>

                        <!-- Maiores despesas -->
                        <?php
                        $sql_despesas = "SELECT descricao, valor, status, data FROM despesas WHERE user_id = ? AND (MONTH(data) = $selectedMonth) ORDER BY valor DESC LIMIT 3";
                        $stmt_despesas = $conn->prepare($sql_despesas);
                        $stmt_despesas->bind_param("i", $user_id);
                        $stmt_despesas->execute();
                        $result_despesas = $stmt_despesas->get_result();

                        if ($result_despesas->num_rows > 0) {
                            echo "<div class='container-resumo'>";
                            echo "<h4>Maiores Despesas</h4>";

                            while ($row = $result_despesas->fetch_assoc()) {
                                echo "<article>";
                                echo "<div class='info'>";
                                echo $row['descricao'];
                                echo "<br>";
                                echo "<div id='container-data-tag'>" . "<p>" . date("d/m/Y", strtotime($row['data'])) . "</p>";
                                if ($row['status'] === "Pago") {
                                    echo "<p id='tag-pago'>Pago</p>";
                                } else {
                                    echo "<p id='tag-pendente'>Pendente</p>";
                                }
                                echo "</div>";
                                echo "</div>";
                                echo "<p class='vermelho'>R$ " . number_format($row['valor'], 2, ',', '.') . "</p>";
                                echo "</article>";
                            }
                            echo "</div>";
                        }
                        ?>

                        <!-- Maiores investimentos -->
                        <?php
                        $sql_investimentos = "SELECT nome, custo, tipo FROM investimentos WHERE user_id = ? AND (MONTH(data) = $selectedMonth) ORDER BY custo DESC LIMIT 3";
                        $stmt_investimentos = $conn->prepare($sql_investimentos);
                        $stmt_investimentos->bind_param("i", $user_id);
                        $stmt_investimentos->execute();
                        $result_investimentos = $stmt_investimentos->get_result();

                        if ($result_investimentos->num_rows > 0) {
                            echo "<div class='container-resumo'>";
                            echo "<h4>Maiores Investimentos</h4>";

                            while ($row = $result_investimentos->fetch_assoc()) {
                                echo "<article>";
                                echo "<div class='info'>";
                                echo $row['nome'];
                                echo "<br>";
                                echo "<p>" . $row['tipo'] . "</p>";
                                echo "</div>";
                                echo "<p class='verde-escuro'>R$ " . number_format($row['custo'], 2, ',', '.') . "</p>";
                                echo "</article>";
                            }
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Gráfico de analise finceira
        let grafico_analise = document.getElementById('grafico_analise').getContext('2d');
        new Chart(grafico_analise, {
            type: 'bar',
            data: {
                labels: ['Rendas', 'Despesas', 'Investimentos'],
                datasets: [{
                    label: 'Valores em R$',
                    data: [
                        <?php echo json_encode($renda_total); ?>,
                        <?php echo json_encode($despesas_pagas_total + $despesas_nao_pagas_total); ?>,
                        <?php echo json_encode($investimentos_total); ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(255, 205, 86, 0.2)'
                    ],
                    borderColor: [
                        'rgb(255, 99, 132)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 205, 86)',
                    ],
                    borderWidth: 1,
                    borderRadius: 0.5
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
            },
        });

        <?php
        // Verificar se é o ultimo dia do ano para gerar relatório anual
        $ultimoDiaAno = (date('m-d') === '12-31');

        if ($ultimoDiaAno && isset($_SESSION['rel_anual']) && $_SESSION['rel_anual'] != true) {
            echo "mostrarAlert()";
        }
        ?>
    </script>
</body>

</html>