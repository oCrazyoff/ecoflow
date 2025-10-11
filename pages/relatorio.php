<?php
$titulo = "Relatório Anual - " . date("Y") - 1;
require_once "includes/inicio.php";

// As funções para os cards de resumo no topo permanecem as mesmas
function totalRendasAnoPassado()
{
    global $conexao;
    $sql = "SELECT SUM(valor) FROM rendas WHERE usuario_id = ? AND YEAR(data) = YEAR(CURDATE()) - 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return $valor ?? 0;
}

function despesasPagasAnoPassado()
{
    global $conexao;
    $sql = "SELECT SUM(valor) FROM despesas WHERE usuario_id = ? AND status = 1 AND YEAR(data) = YEAR(CURDATE()) - 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return $valor ?? 0;
}

function despesasPendentesAnoPassado()
{
    global $conexao;
    $sql = "SELECT SUM(valor) FROM despesas WHERE usuario_id = ? AND status = 0 AND YEAR(data) = YEAR(CURDATE()) - 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return $valor ?? 0;
}

// Array para converter número do mês em nome
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$ano_passado = date('Y') - 1;
?>
    <style>
        /* Estilos para impressão */
        @media print {
            .header-dashboard button {
                display: none;
            }

            .header-dashboard .txt-header {
                justify-self: flex-start;
            }

            .container-cards, .container-meses {
                grid-template-columns: 1fr;
            }

            span[class*="bg-"], i {
                -webkit-print-color-adjust: exact; /* Para Chrome, Safari, etc. */
                print-color-adjust: exact; /* Padrão */
            }
        }
    </style>
    <!--div processando-->
    <div id="processando"
         class="hidden absolute top-0 left-0 flex items-center justify-center bg-black/50 h-full w-full z-500">
        <p class="text-white text-2xl">Processando...</p>
    </div>
    <!--alerta inicio-->
    <div id="alerta-inicio"
         class="fixed top-0 left-0 flex items-center justify-center bg-black/50 h-full w-full z-500">
        <div class="flex flex-col items-center justify-center gap-5 p-5 lg:p-10 rounded-xl w-5/6 lg:w-1/3 bg-yellow-100 border
        border-yellow-500">
            <i class="bi bi-exclamation-triangle text-6xl text-yellow-500"></i>
            <h2 class="text-center text-2xl text-yellow-600 font-bold">Dados não salvos!</h2>
            <p class="text-center text-lg text-yellow-700">
                Existem dados do ano passado que não foram salvos. Por favor, revise e salve suas informações antes de
                gerar o relatório final.
            </p>
            <button class="px-5 py-2 bg-yellow-500 text-white rounded-lg cursor-pointer hover:bg-yellow-600"
                    onclick="fecharAlertaInicio()">Confirmar
            </button>
        </div>
    </div>

    <!--alerta relatorio-->
    <div id="alerta-relatorio"
         class="hidden fixed top-0 left-0 flex items-center justify-center bg-black/50 h-full w-full z-500">
        <div class="flex flex-col items-center justify-center gap-5 p-5 lg:p-10 rounded-xl w-5/6 lg:w-1/3 bg-white border border-borda
        shadow-xl">
            <i class="bi bi-exclamation-triangle text-6xl text-verde"></i>
            <h2 class="text-center text-3xl font-bold">Atenção</h2>
            <p class="text-center text-lg text-texto-opaco">
                Ao gerar o relatório, todos os dados do ano passado serão deletados. Esta ação não pode ser
                desfeita. Deseja continuar?
            </p>
            <div class="flex items-center justify-center gap-5 w-full">
                <button class="px-5 py-2 bg-verde text-white rounded-lg cursor-pointer w-1/2 hover:bg-verde-hover"
                        onclick="gerarRelatorio()">Confirmar
                </button>
                <button class="px-5 py-2 bg-white rounded-lg cursor-pointer border border-borda w-1/2 hover:bg-gray-200"
                        onclick="fecharAlertaRelatorio()">Cancelar
                </button>
            </div>
        </div>
    </div>
    <main>
        <header class="header-dashboard px-7 flex-col lg:flex-row">
            <div class="txt-header self-start">
                <h2>Relatório Anual - <?= $ano_passado ?></h2>
                <p>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>! Aqui está o resumo detalhado das suas finanças do
                    ano <?= $ano_passado ?>.</p>
            </div>
            <button class="text-white bg-verde px-5 py-2 rounded-lg cursor-pointer w-full lg:w-auto mt-3 lg:mt-0
                            hover:bg-verde-hover"
                    onclick="mostrarAlertaRelatorio()">
                <i class="bi bi-printer mr-3"></i> Exportar PDF
            </button>
        </header>

        <!-- Cards de Resumo Total -->
        <div class="container-cards">
            <div class="card">
                <p>Total de Rendas</p>
                <h3><?= formatarReais(totalRendasAnoPassado()) ?></h3>
                <i class="bi bi-cash-stack text-verde bg-verde/10"></i>
            </div>
            <div class="card">
                <p>Despesas Pagas</p>
                <h3><?= formatarReais(despesasPagasAnoPassado()) ?></h3>
                <i class="bi bi-graph-down-arrow text-verde bg-verde/10"></i>
            </div>
            <div class="card">
                <p>Despesas Pendentes</p>
                <h3><?= formatarReais(despesasPendentesAnoPassado()) ?></h3>
                <i class="bi bi-currency-dollar text-verde bg-verde/10"></i>
            </div>
        </div>

        <!-- Container com os cards de cada mês -->
        <div class="grid grid-cols-1 gap-5 px-5 pb-5">
            <?php for ($mes = 1; $mes <= 12; $mes++): ?>
                <?php
                // Busca rendas do mês
                $sql_rendas = "SELECT descricao, data, valor FROM rendas WHERE usuario_id = ? AND YEAR(data) = ? AND MONTH(data) = ? ORDER BY data ASC";
                $stmt_rendas = $conexao->prepare($sql_rendas);
                $stmt_rendas->bind_param("iis", $_SESSION['id'], $ano_passado, $mes);
                $stmt_rendas->execute();
                $resultado_rendas = $stmt_rendas->get_result();
                $stmt_rendas->close();

                // Busca despesas do mês
                $sql_despesas = "SELECT descricao, data, valor, categoria, status FROM despesas WHERE usuario_id = ? AND YEAR(data) = ? AND MONTH(data) = ? ORDER BY data ASC";
                $stmt_despesas = $conexao->prepare($sql_despesas);
                $stmt_despesas->bind_param("iis", $_SESSION['id'], $ano_passado, $mes);
                $stmt_despesas->execute();
                $resultado_despesas = $stmt_despesas->get_result();
                $stmt_despesas->close();
                ?>

                <!-- Só exibe o card do mês se houver alguma movimentação -->
                <?php if ($resultado_rendas->num_rows > 0 || $resultado_despesas->num_rows > 0): ?>
                    <div class="bg-white rounded-xl p-10 border border-borda shadow-lg">
                        <h3 class="text-2xl font-bold text-center"><?= htmlspecialchars($meses[$mes]) ?></h3>
                        <div>

                            <!-- Seção de Rendas -->
                            <?php if ($resultado_rendas->num_rows > 0): ?>
                                <h4 class="text-xl my-5 text-texto-opaco font-semibold border-b-2 border-gray-500 pb-2">
                                    Rendas</h4>
                                <?php while ($renda = $resultado_rendas->fetch_assoc()): ?>
                                    <div class="flex items-center justify-between py-2 border-b border-gray-300">
                                        <div>
                                            <p class="font-semibold"><?= htmlspecialchars($renda['descricao']) ?></p>
                                            <span class="text-texto-opaco"><?= htmlspecialchars(formatarData($renda['data'])) ?></span>
                                        </div>
                                        <p class="text-green-500 font-semibold"><?= htmlspecialchars(formatarReais($renda['valor'])) ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>

                            <!-- Seção de Despesas -->
                            <?php if ($resultado_despesas->num_rows > 0): ?>
                                <h4 class="text-xl my-5 text-texto-opaco font-semibold border-b-2 border-gray-500 pb-2">
                                    Despesas</h4>
                                <?php while ($despesa = $resultado_despesas->fetch_assoc()): ?>
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
                                            <span class="text-texto-opaco"><?= htmlspecialchars(tipoCategorias($despesa['categoria'])) ?></span>
                                        </div>
                                        <p class="text-red-500 font-semibold"><?= htmlspecialchars(formatarReais($despesa['valor'])) ?></p>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </main>
    <script>
        // fechar alerta inicio
        function fecharAlertaInicio() {
            const alerta = document.getElementById("alerta-inicio");

            alerta.classList.add("hidden");
        }

        // fechar alerta relatorio
        function fecharAlertaRelatorio() {
            const alerta = document.getElementById("alerta-relatorio");

            alerta.classList.add("hidden");
        }

        // mostrar alerta relatorio
        function mostrarAlertaRelatorio() {
            const alerta = document.getElementById("alerta-relatorio");

            alerta.classList.remove("hidden");
        }

        // gerar relatório
        function gerarRelatorio() {
            fecharAlertaRelatorio();

            // Define uma função que será chamada DEPOIS que a janela de impressão for fechada
            const afterPrintHandler = async () => {
                window.removeEventListener('afterprint', afterPrintHandler);

                try {
                    // Mostra um feedback visual para o usuário
                    document.getElementById('processando').classList.remove('hidden');

                    const response = await fetch('finalizar_relatorio', {
                        method: 'POST',
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        // Se tudo deu certo no back-end, redireciona para o dashboard.
                        window.location.href = '<?= BASE_URL . 'dashboard' ?>';
                    } else {
                        // caso houver erro
                        alert('Ocorreu um erro ao finalizar o ano. Seus dados não foram apagados. Tente novamente.');
                        document.getElementById('processando').classList.add('hidden');
                    }

                } catch (error) {
                    alert('Ocorreu um erro de conexão. Verifique sua internet e tente novamente.');
                    document.getElementById('processando').classList.add('hidden');
                }
            };

            // Adiciona o "ouvinte" do evento. Ele ficará esperando a janela de impressão fechar.
            window.addEventListener('afterprint', afterPrintHandler, {once: true});

            // Finalmente, chama a janela de impressão.
            window.print();
        }
    </script>
<?php require_once "includes/fim.php" ?>