<?php
$titulo = "Dashboard";
require_once "includes/layout/inicio.php";
require_once "includes/dashboard/queries.php";
require_once "api/ia.php";

// ──────────────────────────────────────────────
// Lógica do assistente IA (mantida do original)
// ──────────────────────────────────────────────
$mes = $_GET['m'] ?? date('m');
$dia = date('d');
// $dia = 16;
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

// ──────────────────────────────────────────────
// Dados da dashboard
// ──────────────────────────────────────────────
$tem_dados = (totalRendas() > 0 || totalDespesas() > 0);
$dados_comparacao = getDadosComparacao();
$dados_categorias = getCategoriasDespesas();
$dados_calendario = getCalendarioFinanceiro();
$dados_historico = getHistorico6Meses();
$dados_semana = getGastoPorSemana();
$dados_parcelas = getResumoParcelas();
$dados_recordes = getRecordes();
$dados_indicadores = getIndicadores();
?>
<main>
    <!-- Header -->
    <header class="header-dashboard">
        <div class="txt-header">
            <h2>Dashboard</h2>
        </div>
        <div class="opt-header">
            <button id="btn-extrato" onclick="mostrarModalExtrato()">
                <i class="bi bi-upload"></i>
                <div class="txt-btn"><span>Importar</span> Extrato</div>
            </button>
            <?php require_once "includes/seletor_mes.php" ?>
        </div>
    </header>

    <?php if ($tem_dados): ?>

        <!-- 1. Comparação com o mês anterior -->
        <?php require "includes/dashboard/comparacao_mes.php" ?>

        <!-- IA mobile (aparece primeiro em telas pequenas) -->
        <div class="dash-ia-mobile block lg:hidden">
            <?php require "includes/dashboard/assistente_ia.php" ?>
        </div>

        <!-- Grid principal -->
        <div class="dashboard-content">

            <!-- Economia do mês (span 4) + IA desktop (span 2) -->
            <div class="col-span-1 lg:col-span-4">
                <?php require "includes/dashboard/economia_mes.php" ?>
            </div>
            <div class="col-span-1 lg:col-span-2 hidden lg:block">
                <?php require "includes/dashboard/assistente_ia.php" ?>
            </div>

            <!-- Ranking (span 3) + Distribuição (span 3) -->
            <div class="col-span-1 lg:col-span-3">
                <?php require "includes/dashboard/ranking_categorias.php" ?>
            </div>
            <div class="col-span-1 lg:col-span-3">
                <?php require "includes/dashboard/distribuicao_gastos.php" ?>
            </div>

            <!-- Calendário (span 3) + Histórico 6 meses (span 3) -->
            <div class="col-span-1 lg:col-span-3">
                <?php require "includes/dashboard/calendario_financeiro.php" ?>
            </div>
            <div class="col-span-1 lg:col-span-3">
                <?php require "includes/dashboard/historico_6meses.php" ?>
            </div>

            <!-- Gasto semana (span 2) + Parcelas (span 2) + Recordes (span 2) -->
            <div class="col-span-1 lg:col-span-2">
                <?php require "includes/dashboard/gasto_semana.php" ?>
            </div>
            <div class="col-span-1 lg:col-span-2">
                <?php require "includes/dashboard/resumo_parcelas.php" ?>
            </div>
            <div class="col-span-1 lg:col-span-2">
                <?php require "includes/dashboard/recordes.php" ?>
            </div>

            <!-- Indicadores (span 3) + Resumo do mês (span 3) -->
            <div class="col-span-1 lg:col-span-3">
                <?php require "includes/dashboard/indicadores.php" ?>
            </div>
            <div class="col-span-1 lg:col-span-3">
                <?php require "includes/dashboard/resumo_mes.php" ?>
            </div>

        </div>

    <?php else: ?>

        <!-- Estado vazio -->
        <div class="dashboard-content">
            <div class="col-span-1 lg:col-span-4">
                <div class="card">
                    <div class="container-mensagem mt-0">
                        <i class="bi bi-piggy-bank icone"></i>
                        <h3 class="titulo">Sem movimentações neste mês</h3>
                        <p class="paragrafo">
                            Adicione sua primeira renda ou despesa para começar a acompanhar seus resultados.
                        </p>
                        <a href="rendas<?= (isset($m) ? '?m=' . $m : '') ?>" class="btn">
                            Registrar Renda
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-span-1 lg:col-span-2">
                <?php require "includes/dashboard/assistente_ia.php" ?>
            </div>
        </div>

    <?php endif; ?>
</main>

<!-- modal importar extrato -->
<?php require_once "includes/modal_extrato.php" ?>

<?php if ($tem_dados): ?>
<script>
    // ──────────────────────────────────────────────
    // Chart.js — Histórico dos últimos 6 meses
    // ──────────────────────────────────────────────
    const ctxHistorico = document.getElementById('historicoChart');
    if (ctxHistorico) {
        new Chart(ctxHistorico, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($dados_historico, 'mes')) ?>,
                datasets: [
                    {
                        label: 'Receitas',
                        data: <?= json_encode(array_column($dados_historico, 'receitas')) ?>,
                        backgroundColor: 'rgba(52, 211, 153, 0.7)',
                        borderColor: 'rgba(52, 211, 153, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Despesas',
                        data: <?= json_encode(array_column($dados_historico, 'despesas')) ?>,
                        backgroundColor: 'rgba(248, 113, 113, 0.7)',
                        borderColor: 'rgba(248, 113, 113, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                borderRadius: 6,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': R$ ' + context.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
<?php endif; ?>
<?php require_once "includes/layout/fim.php" ?>