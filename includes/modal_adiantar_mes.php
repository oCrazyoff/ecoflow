<?php
/**
 * Modal de Adiantar Mês
 * 
 * Exibe despesas/rendas recorrentes do mês anterior para copiar ao mês selecionado.
 * Usado nas páginas de despesas e rendas quando um mês futuro está selecionado.
 * 
 * Variáveis necessárias:
 *   $m - mês selecionado (já definido pelo seletor_mes.php)
 *   $tipo_adiantar - 'despesas' ou 'rendas'
 */

$mesAnterior = $m - 1;
$anoAnterior = (int)date('Y');
if ($mesAnterior < 1) {
    $mesAnterior = 12;
    $anoAnterior--;
}

$nomesMesesAdiantar = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];
$nomeMesAnterior = $nomesMesesAdiantar[$mesAnterior] ?? '';
$nomeMesAtual = $nomesMesesAdiantar[$m] ?? '';
$labelTipo = ($tipo_adiantar === 'rendas') ? 'rendas' : 'despesas';
$corTipo = ($tipo_adiantar === 'rendas') ? 'emerald' : 'purple';
?>

<div id="modal-adiantar-mes" class="hidden fixed inset-0 flex items-center justify-center bg-black/50 z-20000">
    <div class="bg-white rounded-xl p-6 w-11/12 md:w-[28rem] shadow-2xl max-h-[85dvh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-<?= $corTipo ?>-100 rounded-full flex items-center justify-center">
                <i class="bi bi-fast-forward-fill text-<?= $corTipo ?>-600 text-lg"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-texto">Adiantar <?= ucfirst($labelTipo) ?></h2>
                <p class="text-sm text-texto-opaco"><?= $nomeMesAnterior ?> → <?= $nomeMesAtual ?></p>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-blue-700">
                <i class="bi bi-info-circle mr-1"></i>
                Selecione as <?= $labelTipo ?> recorrentes de <strong><?= $nomeMesAnterior ?></strong> que deseja copiar para <strong><?= $nomeMesAtual ?></strong>.
            </p>
        </div>

        <!-- Lista de itens (carregada via AJAX) -->
        <div id="adiantar-lista" class="flex-1 overflow-y-auto mb-4 min-h-[100px]">
            <div class="flex flex-col items-center justify-center py-8 gap-2">
                <i class="bi bi-arrow-repeat animate-spin text-2xl text-texto-opaco"></i>
                <p class="text-sm text-texto-opaco">Carregando <?= $labelTipo ?> recorrentes...</p>
            </div>
        </div>

        <!-- Botões -->
        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-borda">
            <button onclick="fecharModalAdiantar()" 
                class="px-4 py-1 lg:py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 cursor-pointer font-medium">
                Cancelar
            </button>
            <button id="btn-confirmar-adiantar" onclick="confirmarAdiantarMes()" disabled
                class="px-4 py-1 lg:py-2.5 bg-verde text-white rounded-lg hover:bg-verde-hover cursor-pointer font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Adiantar Selecionados
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    var tipoAdiantar = <?= json_encode($tipo_adiantar) ?>;
    var mesAnterior = <?= json_encode($mesAnterior) ?>;
    var mesDestino = <?= json_encode($m) ?>;

    // Abrir modal e carregar itens
    window.abrirModalAdiantarMes = function() {
        document.getElementById('modal-adiantar-mes').classList.remove('hidden');
        carregarItensRecorrentes();
    };

    // Fechar modal
    window.fecharModalAdiantar = function() {
        document.getElementById('modal-adiantar-mes').classList.add('hidden');
    };

    // Fechar ao clicar fora
    document.getElementById('modal-adiantar-mes').addEventListener('click', function(e) {
        if (e.target === this) fecharModalAdiantar();
    });

    // Carregar itens recorrentes via AJAX
    function carregarItensRecorrentes() {
        var lista = document.getElementById('adiantar-lista');
        lista.innerHTML = '<div class="flex flex-col items-center justify-center py-8 gap-2">' +
            '<i class="bi bi-arrow-repeat animate-spin text-2xl text-texto-opaco"></i>' +
            '<p class="text-sm text-texto-opaco">Carregando...</p></div>';

        fetch('buscar_recorrentes_mes?tipo=' + encodeURIComponent(tipoAdiantar) + '&m=' + encodeURIComponent(mesAnterior))
            .then(function(resp) { return resp.json(); })
            .then(function(data) {
                if (!data.sucesso || !data.itens || data.itens.length === 0) {
                    lista.innerHTML = '<div class="flex flex-col items-center justify-center py-8 gap-2">' +
                        '<i class="bi bi-inbox text-3xl text-gray-300"></i>' +
                        '<p class="text-sm text-texto-opaco">Nenhum item recorrente encontrado no mês anterior.</p></div>';
                    return;
                }

                var html = '<div class="flex flex-col gap-2">';

                // Botão selecionar todos
                html += '<label class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 border border-gray-200 cursor-pointer hover:bg-gray-100 mb-1">' +
                    '<input type="checkbox" id="adiantar-todos" onchange="toggleTodosAdiantar(this)" class="w-4 h-4 accent-emerald-500">' +
                    '<span class="text-sm font-semibold text-texto">Selecionar todos</span></label>';

                data.itens.forEach(function(item) {
                    var disabled = item.ja_adiantado;
                    var opacidade = disabled ? 'opacity-50' : '';
                    var cursor = disabled ? 'cursor-not-allowed' : 'cursor-pointer hover:bg-gray-50';

                    html += '<label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 ' + cursor + ' ' + opacidade + '">';
                    html += '<input type="checkbox" name="adiantar-item" value="' + item.id + '"' +
                        (disabled ? ' disabled' : '') +
                        ' onchange="atualizarBotaoAdiantar()" class="w-4 h-4 accent-emerald-500 flex-shrink-0">';
                    html += '<div class="flex-1 min-w-0">';
                    html += '<div class="font-semibold text-sm truncate">' + escapeHtmlAdiantar(item.descricao) + '</div>';
                    html += '<div class="flex items-center gap-2 text-xs text-texto-opaco mt-0.5">';
                    
                    if (item.nome_categoria) {
                        html += '<span class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">' + escapeHtmlAdiantar(item.nome_categoria) + '</span>';
                    }
                    
                    if (disabled) {
                        html += '<span class="bg-green-50 text-green-600 px-1.5 py-0.5 rounded">Já adiantado</span>';
                    }
                    html += '</div></div>';

                    var corValor = tipoAdiantar === 'rendas' ? 'text-green-500' : 'text-red-500';
                    html += '<span class="font-bold text-sm whitespace-nowrap ' + corValor + '">' + formatarReaisAdiantar(item.valor) + '</span>';
                    html += '</label>';
                });

                html += '</div>';
                lista.innerHTML = html;
                atualizarBotaoAdiantar();
            })
            .catch(function() {
                lista.innerHTML = '<div class="flex flex-col items-center justify-center py-8 gap-2">' +
                    '<i class="bi bi-exclamation-circle text-3xl text-red-300"></i>' +
                    '<p class="text-sm text-texto-opaco">Erro ao carregar itens. Tente novamente.</p></div>';
            });
    }

    // Toggle selecionar todos
    window.toggleTodosAdiantar = function(checkbox) {
        var itens = document.querySelectorAll('input[name="adiantar-item"]:not(:disabled)');
        itens.forEach(function(item) { item.checked = checkbox.checked; });
        atualizarBotaoAdiantar();
    };

    // Atualizar estado do botão confirmar
    window.atualizarBotaoAdiantar = function() {
        var selecionados = document.querySelectorAll('input[name="adiantar-item"]:checked');
        var btn = document.getElementById('btn-confirmar-adiantar');
        btn.disabled = selecionados.length === 0;
        
        if (selecionados.length > 0) {
            btn.textContent = 'Adiantar ' + selecionados.length + ' ' + (selecionados.length === 1 ? 'item' : 'itens');
        } else {
            btn.textContent = 'Adiantar Selecionados';
        }
    };

    // Confirmar adiantamento
    window.confirmarAdiantarMes = function() {
        var selecionados = document.querySelectorAll('input[name="adiantar-item"]:checked');
        if (selecionados.length === 0) return;

        var ids = [];
        selecionados.forEach(function(cb) { ids.push(parseInt(cb.value)); });

        var btn = document.getElementById('btn-confirmar-adiantar');
        btn.disabled = true;
        btn.textContent = 'Processando...';

        fetch('adiantar_mes', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tipo: tipoAdiantar,
                ids: ids,
                mes_destino: mesDestino
            })
        })
        .then(function(resp) { return resp.json(); })
        .then(function(data) {
            if (data.sucesso) {
                fecharModalAdiantar();
                location.reload();
            } else {
                alert(data.mensagem || 'Erro ao processar adiantamento.');
                btn.disabled = false;
                btn.textContent = 'Adiantar Selecionados';
            }
        })
        .catch(function() {
            alert('Erro de conexão. Tente novamente.');
            btn.disabled = false;
            btn.textContent = 'Adiantar Selecionados';
        });
    };

    // Helpers
    function escapeHtmlAdiantar(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function formatarReaisAdiantar(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
})();
</script>
