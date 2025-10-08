<?php
$titulo = "Dashboard";
require_once "includes/inicio.php";

// variavel mês
$m = $_GET['m'] ?? NULL;

function totalRendas()
{
    global $conexao;

    // pega o mês do GET ou usa o mês atual
    if (isset($_GET['m']) && is_numeric($_GET['m'])) {
        $mes = $_GET['m'];
    } else {
        $mes = date('m'); // Mês atual
    }

    // filtra apenas pelo mês
    $sql = "SELECT SUM(valor) FROM rendas WHERE usuario_id = ? AND MONTH(data) = ?";
    $stmt = $conexao->prepare($sql);

    $stmt->bind_param("ii", $_SESSION['id'], $mes);

    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();

    return $valor ?? 0;
}

function despesasPagas()
{
    global $conexao;

    if (isset($_GET['m']) && is_numeric($_GET['m'])) {
        $mes = $_GET['m'];
    } else {
        $mes = date('m');
    }

    $sql = "SELECT SUM(valor) FROM despesas WHERE usuario_id = ? AND status = 1 AND MONTH(data) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['id'], $mes);

    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();

    return $valor ?? 0;
}

function despesasPendentes()
{
    global $conexao;

    if (isset($_GET['m']) && is_numeric($_GET['m'])) {
        $mes = $_GET['m'];
    } else {
        $mes = date('m');
    }

    $sql = "SELECT SUM(valor) FROM despesas WHERE usuario_id = ? AND status = 0 AND MONTH(data) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['id'], $mes);

    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();

    return $valor ?? 0;
}

?>
    <main>
        <header class="header-dashboard">
            <div class="txt-header">
                <h2>Dashboard</h2>
                <p>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>! Aqui está o resumo das suas finanças</p>
            </div>
            <div class="opt-header">
                <?php require_once "includes/seletor_mes.php" ?>
                <button id="btn-relatorio"><i class="bi bi-file-earmark-bar-graph"></i> Relatório</button>
            </div>
        </header>
        <div class="container-cards">
            <div class="card">
                <p>Total de Rendas</p>
                <h3><?= formatarReais(totalRendas()) ?></h3>
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="card">
                <p>Despesas Pagas</p>
                <h3><?= formatarReais(despesasPagas()) ?></h3>
                <i class="bi bi-graph-down-arrow"></i>
            </div>
            <div class="card">
                <p>Despesas Pendentes</p>
                <h3><?= formatarReais(despesasPendentes()) ?></h3>
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="meio-dashboard">
            <div class="card col-span-3">
                <?php if (totalRendas() > 0 || despesasPagas() > 0 || despesasPendentes() > 0): ?>
                    <h3>Análise Financeira</h3>
                    <div class="grafico-analise">
                        <canvas id="resumoMensalChart"></canvas>
                    </div>
                <?php else: ?>
                    <div class="container-mensagem">
                        <i class="bi bi-piggy-bank icone">
                        </i>
                        <h3 class="titulo">Sem movimentações neste mês</h3>
                        <p class="paragrafo">
                            Adicione sua primeira renda ou despesa para começar a acompanhar seus resultados.
                        </p>
                        <a href="rendas"
                           class="btn"
                           onclick="abrirCadastrarModal('rendas')">Registrar Renda
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card">
                <?php if (totalRendas() > 0 || despesasPagas() > 0 || despesasPendentes() > 0): ?>
                    <h3>Resumo Financeiro</h3>
                    <?php
                    // puxando as 5 maiores rendas do usuario
                    if (isset($m) && $m > 0 && $m < 13) {
                        $sql = "SELECT descricao, data, valor 
                                FROM rendas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = ?
                                ORDER BY valor 
                                DESC LIMIT 5";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("is", $_SESSION['id'], $m);
                    } else {
                        $sql = "SELECT descricao, data, valor 
                                FROM rendas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = MONTH(CURDATE())
                                ORDER BY valor 
                                DESC LIMIT 5";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['id']);
                    }
                    $stmt->execute();
                    $resultado_rendas = $stmt->get_result();
                    $stmt->close();

                    if ($resultado_rendas->num_rows > 0):?>
                        <h4 class="titulo-resumo">Maiores Rendas</h4>
                        <?php while ($renda = $resultado_rendas->fetch_assoc()): ?>
                            <div class="item-resumo">
                                <div class="txt-resumo">
                                    <h5><?= htmlspecialchars($renda['descricao']) ?></h5>
                                    <p><?= htmlspecialchars(formatarData($renda['data'])) ?></p>
                                </div>
                                <p class="text-green-500"><?= htmlspecialchars(formatarReais($renda['valor'])) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    <?php
                    // puxando as 5 maiores despesas do usuario
                    if (isset($m) && $m > 0 && $m < 13) {
                        $sql = "SELECT descricao, status, categoria, valor
                                FROM despesas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = ?
                                ORDER BY valor DESC 
                                LIMIT 5";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("is", $_SESSION['id'], $m);
                    } else {
                        $sql = "SELECT descricao, status, categoria, valor
                                FROM despesas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = MONTH(CURDATE())
                                ORDER BY valor DESC 
                                LIMIT 5";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['id']);
                    }
                    $stmt->execute();
                    $resultado_despesas = $stmt->get_result();
                    $stmt->close();

                    if ($resultado_despesas->num_rows > 0):?>
                        <h4 class="titulo-resumo">Maiores Despesas</h4>
                        <?php while ($despesa = $resultado_despesas->fetch_assoc()): ?>
                            <div class="item-resumo">
                                <div class="txt-resumo">
                                    <h5>
                                        <?= htmlspecialchars($despesa['descricao']) ?>
                                        <?php if ($despesa['status'] == 0): ?>
                                            <span class="bg-yellow-500">Pendente</span>
                                        <?php else: ?>
                                            <span class="bg-green-500">Pago</span>
                                        <?php endif; ?>
                                    </h5>
                                    <p><?= htmlspecialchars(tipoCategorias($despesa['categoria'])) ?></p>
                                </div>
                                <p class="text-red-500"><?= htmlspecialchars(formatarReais($despesa['valor'])) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="container-mensagem flex items-center justify-center h-full">
                        <i class="bi bi-cup-hot icone"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script>
        const ctx = document.getElementById('resumoMensalChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                // Rótulos para o eixo X (cada uma das barras)
                labels: ['Rendas', 'Despesas Pagas', 'Despesas Pendentes'],
                datasets: [{
                    label: 'R$',

                    // dados injetados diretamente do PHP
                    data: [
                        <?= totalRendas() ?>,
                        <?= despesasPagas() ?>,
                        <?= despesasPendentes() ?>,
                    ],

                    // Cores de fundo para cada barra
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                borderRadius: 10,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true // Garante que o eixo Y comece no zero
                    }
                }
            }
        });
    </script>
<?php require_once "includes/fim.php" ?>