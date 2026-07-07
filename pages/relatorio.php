<?php
$n_valida = false;
$titulo = "Relatório Anual";
require_once "includes/layout/inicio.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "relatorios");
    exit();
}

$relatorio_id = (int)$_GET['id'];

// Busca o relatório no banco
$sql = "SELECT ano, dados_json FROM relatorios_anuais WHERE id = ? AND usuario_id = ? AND status = 'GERADO'";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $relatorio_id, $_SESSION['id']);
$stmt->execute();
$resultado = $stmt->get_result();
$stmt->close();

if ($resultado->num_rows === 0) {
    header("Location: " . BASE_URL . "relatorios");
    exit();
}

$relatorio = $resultado->fetch_assoc();
$ano = $relatorio['ano'];
$dados = json_decode($relatorio['dados_json'], true);

$totais = $dados['totais'];
$meses_data = $dados['meses'];

$meses_nomes = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
?>
<style>
    @media print {
        .header-dashboard button,
        .btn-voltar {
            display: none !important;
        }

        .header-dashboard .txt-header {
            justify-self: flex-start;
        }

        .container-cards,
        .container-meses {
            grid-template-columns: 1fr;
        }

        span[class*="bg-"], i {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>
<main>
    <header class="header-dashboard px-7 flex-col lg:flex-row">
        <div class="txt-header self-start">
            <a href="<?= BASE_URL ?>relatorios" class="btn-voltar text-sm text-gray-500 hover:text-verde mb-2 inline-block">
                <i class="bi bi-arrow-left"></i> Voltar para Relatórios
            </a>
            <h2>Relatório Anual - <?= $ano ?></h2>
            <p>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>! Aqui está o resumo detalhado das suas finanças do ano <?= $ano ?>.</p>
        </div>
        <button class="text-white bg-verde px-5 py-2 rounded-lg cursor-pointer w-full lg:w-auto mt-3 lg:mt-0 hover:bg-verde-hover" onclick="window.print()">
            <i class="bi bi-printer mr-3"></i> Imprimir / Baixar PDF
        </button>
    </header>

    <!-- Cards de Resumo Total -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 p-5">
        <div class="bg-white rounded-xl p-5 border border-borda shadow-sm flex flex-col justify-center items-start relative overflow-hidden">
            <p class="text-gray-500 font-semibold mb-1">Total de Rendas</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= formatarReais($totais['receitas']) ?></h3>
            <i class="bi bi-cash-stack absolute right-5 top-1/2 -translate-y-1/2 text-3xl text-verde bg-verde/10 p-3 rounded-xl"></i>
        </div>
        <div class="bg-white rounded-xl p-5 border border-borda shadow-sm flex flex-col justify-center items-start relative overflow-hidden">
            <p class="text-gray-500 font-semibold mb-1">Despesas Pagas</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= formatarReais($totais['despesas_pagas']) ?></h3>
            <i class="bi bi-graph-down-arrow absolute right-5 top-1/2 -translate-y-1/2 text-3xl text-verde bg-verde/10 p-3 rounded-xl"></i>
        </div>
        <div class="bg-white rounded-xl p-5 border border-borda shadow-sm flex flex-col justify-center items-start relative overflow-hidden">
            <p class="text-gray-500 font-semibold mb-1">Despesas Pendentes</p>
            <h3 class="text-2xl font-bold text-gray-800"><?= formatarReais($totais['despesas_pendentes']) ?></h3>
            <i class="bi bi-currency-dollar absolute right-5 top-1/2 -translate-y-1/2 text-3xl text-verde bg-verde/10 p-3 rounded-xl"></i>
        </div>
    </div>

    <!-- Container com os cards de cada mês -->
    <div class="grid grid-cols-1 gap-5 px-5 pb-5">
        <?php foreach ($meses_data as $mes_num => $mes_info): 
            $total_rendas_mes = array_sum(array_column($mes_info['rendas'], 'valor'));
            $total_despesas_mes = array_sum(array_column($mes_info['despesas'], 'valor'));
            $saldo_mes = $total_rendas_mes - $total_despesas_mes;
        ?>
            <div class="bg-white rounded-xl p-10 border border-borda shadow-lg">
                <h3 class="text-2xl font-bold text-center">
                    <?= htmlspecialchars($meses_nomes[$mes_num]) ?>
                    <span class="text-verde">•</span>
                    <span class="<?= $saldo_mes >= 0 ? 'text-emerald-500' : 'text-red-500' ?>">
                        <?= formatarReais($saldo_mes) ?>
                    </span>
                </h3>
                <div>
                    <!-- Seção de Rendas -->
                    <?php if (!empty($mes_info['rendas'])): ?>
                        <h4 class="text-xl my-5 text-texto-opaco font-semibold border-b-2 border-gray-500 pb-2">Rendas</h4>
                        <?php foreach ($mes_info['rendas'] as $renda): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-300">
                                <div>
                                    <p class="font-semibold"><?= htmlspecialchars($renda['descricao']) ?></p>
                                    <span class="text-texto-opaco"><?= htmlspecialchars(formatarData($renda['data'])) ?></span>
                                </div>
                                <p class="text-green-500 font-semibold"><?= htmlspecialchars(formatarReais($renda['valor'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Seção de Despesas -->
                    <?php if (!empty($mes_info['despesas'])): ?>
                        <h4 class="text-xl my-5 text-texto-opaco font-semibold border-b-2 border-gray-500 pb-2">Despesas</h4>
                        <?php foreach ($mes_info['despesas'] as $despesa): ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-300">
                                <div>
                                    <p class="font-semibold">
                                        <?= htmlspecialchars($despesa['descricao']) ?>
                                        <?php if ($despesa['status'] == 0): ?>
                                            <span class="text-sm px-3 rounded-full bg-[#EFB101] text-white">Pendente</span>
                                        <?php else: ?>
                                            <span class="text-sm px-3 rounded-full bg-[#00C951] text-white">Pago</span>
                                        <?php endif; ?>
                                    </p>
                                    <span class="text-texto-opaco">
                                        <?= htmlspecialchars($despesa['categoria_nome'] ?? 'Sem Categoria') ?>
                                    </span>
                                </div>
                                <p class="text-red-500 font-semibold"><?= htmlspecialchars(formatarReais($despesa['valor'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php require_once "includes/layout/fim.php" ?>