<?php
// Modal de adiantamento de despesa recorrente
$mesAtualNum = (int)date('n');
$anoAtual = (int)date('Y');
$nomesMesesModal = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                    7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
?>

<div id="modal-adiantamento" class="hidden fixed top-0 left-0 flex items-center justify-center bg-black/50 h-full w-full z-500">
    <div class="bg-white rounded-xl p-6 w-11/12 md:w-96 shadow-2xl">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="bi bi-fast-forward-fill text-purple-600 text-lg"></i>
            </div>
            <h2 class="text-xl font-bold text-texto">Adiantar Despesa</h2>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
            <p class="text-sm text-gray-500 mb-1">Despesa</p>
            <p id="adiant-descricao" class="font-bold text-texto text-lg"></p>
            <p class="text-sm text-gray-500 mt-2 mb-1">Valor</p>
            <p id="adiant-valor" class="font-bold text-red-500 text-lg"></p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2" for="adiant-mes">Mês de referência (destino)</label>
            <select id="adiant-mes" class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-texto focus:ring-2 focus:ring-purple-300 focus:border-purple-400 outline-none">
                <?php 
                // Mostrar os próximos 6 meses
                for ($i = 1; $i <= 6; $i++):
                    $mesOpcao = $mesAtualNum + $i;
                    $anoOpcao = $anoAtual;
                    if ($mesOpcao > 12) {
                        $mesOpcao -= 12;
                        $anoOpcao++;
                    }
                ?>
                    <option value="<?= $mesOpcao ?>" data-ano="<?= $anoOpcao ?>">
                        <?= $nomesMesesModal[$mesOpcao] ?>/<?= $anoOpcao ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 mb-4">
            <p class="text-sm text-purple-700">
                <i class="bi bi-info-circle mr-1"></i>
                O pagamento será registrado no mês atual e a despesa do mês selecionado será marcada como paga antecipadamente.
            </p>
        </div>

        <input type="hidden" id="adiant-despesa-id" value="">

        <div class="grid grid-cols-2 gap-3">
            <button onclick="fecharModalAdiantamento()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 cursor-pointer font-medium">
                Cancelar
            </button>
            <button onclick="confirmarAdiantamento()" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 cursor-pointer font-medium">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
    function abrirModalAdiantamento(id, descricao, valor) {
        document.getElementById('adiant-despesa-id').value = id;
        document.getElementById('adiant-descricao').textContent = descricao;
        
        // Formatar valor em reais
        const valorFormatado = parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
        document.getElementById('adiant-valor').textContent = '- ' + valorFormatado;
        
        document.getElementById('modal-adiantamento').classList.remove('hidden');
    }

    function fecharModalAdiantamento() {
        document.getElementById('modal-adiantamento').classList.add('hidden');
    }

    function confirmarAdiantamento() {
        const despesaId = document.getElementById('adiant-despesa-id').value;
        const selectMes = document.getElementById('adiant-mes');
        const mesDestino = selectMes.value;
        const anoDestino = selectMes.options[selectMes.selectedIndex].dataset.ano;

        // Desabilitar botão para evitar duplo clique
        const btnConfirmar = event.target;
        btnConfirmar.disabled = true;
        btnConfirmar.textContent = 'Processando...';

        fetch("cadastrar_adiantamento", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                despesa_id: parseInt(despesaId),
                mes_destino: parseInt(mesDestino),
                ano_destino: parseInt(anoDestino)
            })
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.sucesso) {
                fecharModalAdiantamento();
                location.reload();
            } else {
                alert(data.mensagem || 'Erro ao processar adiantamento.');
                btnConfirmar.disabled = false;
                btnConfirmar.textContent = 'Confirmar';
            }
        })
        .catch(() => {
            alert('Erro de conexão. Tente novamente.');
            btnConfirmar.disabled = false;
            btnConfirmar.textContent = 'Confirmar';
        });
    }
</script>
