<?php
$titulo = "Dashboard";
require_once "includes/inicio.php";
require_once "api/deepseek.php";

function totalRendas()
{
    global $conexao;

    // pega o mês do GET ou usa o mês atual
    if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m'] > 0 && $_GET['m'] < 13) {
        $mes = $_GET['m'];
    } else {
        $mes = date('m'); // Mês atual
    }

    // filtra apenas pelo mês
    $sql = "SELECT SUM(valor) FROM rendas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
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

    // pega o mês do GET ou usa o mês atual
    if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m'] > 0 && $_GET['m'] < 13) {
        $mes = $_GET['m'];
    } else {
        $mes = date('m'); // Mês atual
    }

    $sql = "SELECT SUM(valor) FROM despesas WHERE usuario_id = ? AND status = 1 AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
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

    // pega o mês do GET ou usa o mês atual
    if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m'] > 0 && $_GET['m'] < 13) {
        $mes = $_GET['m'];
    } else {
        $mes = date('m'); // Mês atual
    }

    $sql = "SELECT SUM(valor) FROM despesas WHERE usuario_id = ? AND status = 0 AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $_SESSION['id'], $mes);

    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();

    return $valor ?? 0;
}

// lógica do assistente IA
$mes = $_GET['m'] ?? date('m');
$dia = date('d');
$dia = 16;
$saldo = totalRendas() - despesasPagas();

// Buscando insights do banco
$sql_ia = "SELECT titulo, mensagem FROM insights WHERE usuario_id = ? AND MONTH(data) = ?";
$stmt_ia = $conexao->prepare($sql_ia);
$stmt_ia->bind_param("is", $_SESSION['id'], $mes);
$stmt_ia->execute();
$resultado_ia = $stmt_ia->get_result();
$dados_ia = $resultado_ia->fetch_assoc();
$stmt_ia->close();

// Variáveis de saída
$titulo_ia = '';
$txt_ia = '';
$expected_title_type = null; // 0=Meta, 1=Parabéns, 2=Alerta

if ($mes == date('m')) {
    // --- MÊS ATUAL ---
    if ($dia <= 15) {
        // Começo do mês: esperado é uma Meta
        $expected_title_type = 0;
    } else {
        // Fim do mês: esperado é Parabéns ou Alerta
        if ($saldo > 0) {
            $expected_title_type = 1; // Parabéns
        } elseif ($saldo < 0) {
            $expected_title_type = 2; // Alerta
        }
    }
} elseif ($mes < date('m')) {
    // --- MÊS PASSADO ---
    // Esperado é o resultado final: Parabéns ou Alerta
    if ($saldo > 0) {
        $expected_title_type = 1; // Parabéns
    } elseif ($saldo < 0) {
        $expected_title_type = 2; // Alerta
    }
}

// Se há um estado esperado (não é mês futuro)
if ($expected_title_type !== null) {

    // CASO 1: Um insight salvo E o tipo dele bate com o esperado
    if ($dados_ia && $dados_ia['titulo'] == $expected_title_type) {
        $txt_ia = $dados_ia['mensagem'];

        // Define o título com base no tipo
        if ($expected_title_type == 0) $titulo_ia = 'Meta Financeira 🎯';
        if ($expected_title_type == 1) $titulo_ia = 'Parabéns ✅';
        if ($expected_title_type == 2) $titulo_ia = 'Alerta ⚠️';

        // CASO 2: Insight salvo OU o tipo não bate (ex: esperava 'Parabéns' mas salvou 'Meta')
    } else {
        // Gera uma nova mensagem com base no tipo esperado
        if ($expected_title_type == 0) {
            $titulo_ia = 'Meta Financeira 🎯';
            $txt_ia = gerarMeta($mes);
        } elseif ($expected_title_type == 1) {
            $titulo_ia = 'Parabéns ✅';
            $txt_ia = gerarSucesso($mes);
        } elseif ($expected_title_type == 2) {
            $titulo_ia = 'Alerta ⚠️';
            $txt_ia = gerarAlerta($mes);
        }
    }

    // Se for começo do mês e o insight salvo NÃO for uma meta, forçar uma meta.
    if ($mes == date('m') && $dia <= 15 && $dados_ia && $dados_ia['titulo'] != 0) {
        $titulo_ia = 'Meta Financeira 🎯';
        $txt_ia = gerarMeta($mes);
    }
}
?>
<main>
    <header class="header-dashboard">
        <div class="txt-header">
            <h2>Dashboard</h2>
            <p>Saldo <span class="text-verde">•</span> <?= formatarReais($saldo) ?></p>
        </div>
        <div class="opt-header">
            <?php require_once "includes/seletor_mes.php" ?>
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
        <div class="block lg:hidden"><?php require "includes/dashboard/assistente_ia.php" ?></div>
        <div class="card col-span-1 lg:col-span-3">
            <?php if (totalRendas() > 0 || despesasPagas() > 0 || despesasPendentes() > 0): ?>
                <h3>Análise Financeira</h3>
                <div>
                    <canvas class="min-w-full h-auto" id="resumoMensalChart"></canvas>
                </div>
            <?php else: ?>
                <div class="container-mensagem mt-0">
                    <i class="bi bi-piggy-bank icone">
                    </i>
                    <h3 class="titulo">Sem movimentações neste mês</h3>
                    <p class="paragrafo">
                        Adicione sua primeira renda ou despesa para começar a acompanhar seus resultados.
                    </p>
                    <a href="rendas<?= (isset($m) ? '?m=' . $m : '') ?>" class="btn">
                        Registrar Renda
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="container-direita">
            <div class="mb-[1.5rem] hidden lg:block"><?php require "includes/dashboard/assistente_ia.php" ?></div>
            <div class="card">
                <?php if (totalRendas() > 0 || despesasPagas() > 0 || despesasPendentes() > 0): ?>
                    <h3>Resumo Financeiro</h3>
                    <?php
                    // puxando as 4 maiores rendas do usuario
                    if (isset($m) && $m > 0 && $m < 13) {
                        $sql = "SELECT descricao, data, valor 
                                FROM rendas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = ?
                                AND YEAR(data) = YEAR(CURDATE())
                                ORDER BY valor 
                                DESC LIMIT 4";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("is", $_SESSION['id'], $m);
                    } else {
                        $sql = "SELECT descricao, data, valor 
                                FROM rendas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = MONTH(CURDATE())
                                AND YEAR(data) = YEAR(CURDATE())
                                ORDER BY valor 
                                DESC LIMIT 5";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['id']);
                    }
                    $stmt->execute();
                    $resultado_rendas = $stmt->get_result();
                    $stmt->close();

                    if ($resultado_rendas->num_rows > 0): ?>
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
                    // puxando as 4 maiores despesas do usuario
                    if (isset($m) && $m > 0 && $m < 13) {
                        $sql = "SELECT descricao, status, categoria, valor
                                FROM despesas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = ?
                                AND YEAR(data) = YEAR(CURDATE())
                                ORDER BY valor DESC 
                                LIMIT 4";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("is", $_SESSION['id'], $m);
                    } else {
                        $sql = "SELECT descricao, status, categoria, valor
                                FROM despesas 
                                WHERE usuario_id = ?
                                AND MONTH(data) = MONTH(CURDATE())
                                AND YEAR(data) = YEAR(CURDATE())
                                ORDER BY valor DESC 
                                LIMIT 5";
                        $stmt = $conexao->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['id']);
                    }
                    $stmt->execute();
                    $resultado_despesas = $stmt->get_result();
                    $stmt->close();

                    if ($resultado_despesas->num_rows > 0): ?>
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
                    <div class="container-mensagem flex items-center justify-center h-full mt-0">
                        <i class="bi bi-cup-hot icone"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php
// Consulta total de despesas por categoria
if (isset($m) && $m > 0 && $m < 13) {
    $sql = "SELECT categoria, SUM(valor) AS total
            FROM despesas
            WHERE usuario_id = ?
            AND MONTH(data) = ?
            AND YEAR(data) = YEAR(CURDATE())
            GROUP BY categoria
            ORDER BY total DESC
            LIMIT 5";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("is", $_SESSION['id'], $m);
} else {
    $sql = "SELECT categoria, SUM(valor) AS total
            FROM despesas
            WHERE usuario_id = ?
            AND MONTH(data) = MONTH(CURDATE())
            AND YEAR(data) = YEAR(CURDATE())
            GROUP BY categoria
            ORDER BY total DESC
            LIMIT 5";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
}

$stmt->execute();
$resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Monta arrays para o gráfico
$labels = [];
$valores = [];

foreach ($resultados as $r) {
    $labels[] = tipoCategorias((int)$r['categoria']);
    $valores[] = (float)$r['total'];
}
?>
<script>
    const ctx = document.getElementById('resumoMensalChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'R$: ',
                data: <?= json_encode($valores) ?>,
                backgroundColor: [
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(100, 181, 246, 0.6)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(100, 181, 246, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            borderRadius: 8,
            plugins: {
                legend: {
                    display: false
                },
            }
        }
    });
</script>
<?php require_once "includes/fim.php" ?>