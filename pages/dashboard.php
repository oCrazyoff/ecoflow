<?php
$titulo = "Dashboard";
require_once "includes/layout/inicio.php";
require_once "includes/dashboard/queries.php";

// ──────────────────────────────────────────────
// Dados da dashboard (tudo síncrono e rápido — apenas SQL local)
// ──────────────────────────────────────────────
$mes = $_GET['m'] ?? date('m');
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
    try {
        const ctxHistorico = document.getElementById('historicoChart');
        if (ctxHistorico && typeof Chart !== 'undefined') {
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
    } catch (e) {
        console.warn('Chart.js não pôde ser inicializado:', e.message);
    }
</script>
<?php endif; ?>

<!-- Script de carregamento assíncrono do Assistente IA -->
<script>
(function() {
    var mes = <?= json_encode($mes) ?>;
    var containers = document.querySelectorAll('.assistente-ia-conteudo');
    if (!containers.length) return;

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function preencherContainers(html) {
        containers.forEach(function(c) { c.innerHTML = html; });
    }

    fetch('<?= BASE_URL ?>obter_insight?m=' + encodeURIComponent(mes))
        .then(function(resp) { return resp.json(); })
        .then(function(data) {
            if (data.sucesso && data.mensagem && data.mensagem.trim() !== '') {
                preencherContainers(
                    '<h4 class="titulo">' + escapeHtml(data.titulo) + '</h4>' +
                    '<p>' + escapeHtml(data.mensagem) + '</p>'
                );
            } else {
                preencherContainers(
                    '<img class="bg-verde/20 rounded-2xl py-1 h-25" src="assets/img/esperar.svg" alt="Desenho de espera">' +
                    '<p class="text-xs text-texto-opaco mt-2">Sem sugestões para este mês.</p>'
                );
            }
        })
        .catch(function() {
            preencherContainers(
                '<p class="text-sm text-texto-opaco text-center py-3">' +
                '<i class="bi bi-exclamation-circle"></i> Não foi possível carregar o insight.</p>'
            );
        });
})();
</script>

<?php require_once "includes/layout/fim.php" ?>