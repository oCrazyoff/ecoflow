<?php
// caso tenha mês selecionado ele é inserido nos inputs data
$ano = date('Y');
$mes = $m ?? date('m');
$dia = date('d');
?>
<!--modal-->
<div id="modal" class="hidden">
    <div id="form-container">
        <h2 id="modal-title" class="text-xl font-bold mb-4"></h2>
        <form id="modal-form" action="#" method="POST">
            <!--CSRF-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
            <?php
            if (isset($tipo_modal)):

                // modal de rendas
                if ($tipo_modal == 'rendas'): ?>

                    <!--conteudo do formulario-->
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="input-modal"
                           placeholder="Ex: Conta de Luz"
                           required>
                    <label for="valor">Valor</label>
                    <input type="text" name="valor" id="valor" class="input-modal" placeholder="0,00"
                           inputmode="numeric">
                    <label for="recorrente">Recorrente</label>
                    <select class="input-modal" name="recorrente" id="recorrente">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                    <label for="data">Data</label>
                    <input class="input-modal" type="date" name="data" id="data"
                           value="<?= sprintf('%04d-%02d-%02d', $ano, $mes, $dia) ?>">

                <?php elseif

                    // modal de despesas
                ($tipo_modal == 'despesas'): ?>

                    <!--conteudo do formulario-->
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="input-modal"
                           placeholder="Ex: Conta de Luz"
                           required>
                    <label for="status">Status</label>
                    <select class="input-modal" name="status" id="status">
                        <option value="0">Pendente</option>
                        <option value="1">Pago</option>
                    </select>
                    <label for="valor">Valor</label>
                    <input type="text" name="valor" id="valor" class="input-modal" placeholder="0,00"
                           inputmode="numeric">
                    <label for="recorrente">Recorrente</label>
                    <select class="input-modal" name="recorrente" id="recorrente">
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
                    </select>
                    <label for="categoria">Categoria</label>
                    <select class="input-modal" name="categoria" id="categoria">
                        <?php
                        $categoria_selecionada = 1;
                        // Gera as opções automaticamente
                        for ($i = 1; $i <= 7; $i++) {
                            $texto = tipoCategorias($i);
                            $selected = ($i == $categoria_selecionada) ? 'selected' : '';
                            echo "<option value='$i' $selected>$texto</option>";
                        }
                        ?>
                        <option value="0" <?= ($categoria_selecionada == 0) ? 'selected' : '' ?>>Outro</option>
                    </select>
                    <label for="data">Data</label>
                    <input class="input-modal" type="date" name="data" id="data"
                           value="<?= sprintf('%04d-%02d-%02d', $ano, $mes, $dia) ?>">
                <?php endif; ?>
            <?php endif; ?>
            <div class="grid grid-cols-2 gap-2 mt-5">
                <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn-submit">Enviar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // funções do modal

    // digitação dinamica valor
    const inputValor = document.getElementById('valor');

    inputValor.addEventListener('input', function () {
        // Remove tudo que não for número
        let valor = this.value.replace(/\D/g, '');

        // Divide por 100 pra ter as casas decimais
        valor = (valor / 100).toFixed(2) + '';

        // Troca o ponto por vírgula e adiciona separador de milhar
        valor = valor.replace('.', ',');
        valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        // Atualiza o campo
        this.value = valor;
    });

    // Capitaliza a primeira letra
    function capitalizarPrimeiraLetra(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function abrirCadastrarModal(tabela) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('modal-form');

        modal.classList.remove('hidden');
        document.getElementById('modal-title').textContent = `Cadastrar ${capitalizarPrimeiraLetra(tabela)}`;

        // limpa campos do form
        form.reset();

        // altera action do form para o PHP de cadastro
        form.action = `cadastrar_${tabela}`;
    }

    async function abrirEditarModal(tabela, id) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('modal-form');
        const modalTitle = document.getElementById('modal-title');

        // Mostrar modal imediatamente
        modal.classList.remove('hidden');

        // Coloca título temporário
        modalTitle.textContent = "Carregando...";

        // Altera action do form
        form.action = `editar_${tabela}?id=${id}`;

        try {
            // Busca os dados
            const resp = await fetch(`buscar_${tabela}?id=${id}`);
            const dados = await resp.json();

            // Preenche os campos do form
            for (const campo in dados) {
                if (form[campo]) form[campo].value = dados[campo];
            }

            // Atualiza título com o correto
            modalTitle.textContent = `Editar ${capitalizarPrimeiraLetra(tabela)}`;
        } catch (erro) {
            modalTitle.textContent = `Erro ao carregar ${capitalizarPrimeiraLetra(tabela)}`;
            console.error("Erro ao buscar dados:", erro);
        }
    }

    function fecharModal(tabela) {
        document.getElementById(`modal`).classList.add('hidden');
    }
</script>