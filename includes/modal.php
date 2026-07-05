<?php
// caso tenha mês selecionado ele é inserido nos inputs data
$ano = date('Y');
$mes = $m ?? date('m');
$dia = date('d');
?>
<!--modal-->
<div id="modal" class="hidden">
    <div id="form-container">
        <div class="flex items-center justify-between mb-4">
            <h2 id="modal-title" class="text-xl font-bold"></h2>
            <button type="button" onclick="fecharModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none cursor-pointer" aria-label="Fechar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="modal-form" action="#" method="POST">
            <!--CSRF-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
            <?php
            if (isset($tipo_modal)):

                // modal de rendas
                if ($tipo_modal == 'rendas'): ?>

                    <!--conteudo do formulario-->
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="input-modal" placeholder="Ex: Salário" required>
                    <label for="valor">Valor</label>
                    <input type="text" name="valor" id="valor" class="input-modal" placeholder="0,00" inputmode="numeric">
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

                    <!-- Seção 1: Informações Gerais -->
                    <div class="modal-section">
                        <h3 class="modal-section-title">Informações Gerais</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label for="valor">Valor</label>
                                <input type="text" name="valor" id="valor" class="input-modal" placeholder="0,00" inputmode="numeric">
                            </div>
                            <div>
                                <label for="descricao">Descrição</label>
                                <input type="text" name="descricao" id="descricao" class="input-modal" placeholder="Ex: Conta de Luz" required>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 2: Categorização -->
                    <div class="modal-section">
                        <h3 class="modal-section-title">Categorização</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label for="categoria">Categoria</label>
                                <select class="input-modal" name="categoria_id" id="categoria_id" required>
                                    <option value="">Selecione</option>
                                    <?php
                                    $categoria_selecionada = isset($row['categoria_id']) ? $row['categoria_id'] : null;
                                    $sql_todas_cat = "SELECT id, nome FROM categorias WHERE usuario_id = ? ORDER BY nome ASC";
                                    if ($stmt_cat = $conexao->prepare($sql_todas_cat)) {
                                        $stmt_cat->bind_param("i", $_SESSION['id']);
                                        $stmt_cat->execute();
                                        $result_cat = $stmt_cat->get_result();
                                        if ($result_cat->num_rows > 0) {
                                            while ($cat = $result_cat->fetch_assoc()) {
                                                $selected = ($cat['id'] == $categoria_selecionada) ? 'selected' : '';
                                                echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['nome']) . "</option>";
                                            }
                                        }
                                        $stmt_cat->close();
                                    } else {
                                        echo "<option value=''>Erro ao carregar</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div>
                                <label for="data">Data</label>
                                <input class="input-modal" type="date" name="data" id="data"
                                    value="<?= sprintf('%04d-%02d-%02d', $ano, $mes, $dia) ?>">
                            </div>
                            <div>
                                <label>Status</label>
                                <input type="hidden" name="status" id="status" value="0">
                                <div class="toggle-group" id="toggle-status">
                                    <button type="button" class="toggle-btn active" data-value="0">Pendente</button>
                                    <button type="button" class="toggle-btn" data-value="1">Pago</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção 3: Detalhes Financeiros -->
                    <div class="modal-section" id="section-detalhes">
                        <h3 class="modal-section-title">Detalhes Financeiros</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div id="container-recorrente">
                                <label>Recorrente</label>
                                <input type="hidden" name="recorrente" id="recorrente" value="0">
                                <div class="toggle-group" id="toggle-recorrente">
                                    <button type="button" class="toggle-btn active" data-value="0">Não</button>
                                    <button type="button" class="toggle-btn" data-value="1">Sim</button>
                                </div>
                            </div>
                            <div id="container-parcelado-wrapper">
                                <label>Parcelado</label>
                                <input type="hidden" name="parcelado" id="parcelado" value="0">
                                <div class="toggle-group" id="toggle-parcelado">
                                    <button type="button" class="toggle-btn active" data-value="0">Não</button>
                                    <button type="button" class="toggle-btn" data-value="1">Sim</button>
                                </div>
                            </div>
                        </div>
                        <div id="container-parcelas" class="hidden mt-3">
                            <label for="num_parcelas">Número de Parcelas</label>
                            <input type="number" name="num_parcelas" id="num_parcelas" class="input-modal" min="2" max="120" placeholder="Ex: 12">
                        </div>
                    </div>

                    <!-- Campo oculto para grupo de parcelas (usado na edição) -->
                    <input type="hidden" name="parcela_grupo" id="parcela_grupo" value="">

                    <!-- Checkbox para editar todas as parcelas -->
                    <div id="container-editar-todas" class="hidden mt-3 p-3 bg-gray-50 rounded-lg border border-borda">
                        <label class="flex items-center gap-2 cursor-pointer text-sm m-0">
                            <input type="checkbox" name="editar_todas" id="editar_todas" value="1" class="accent-verde w-4 h-4">
                            <span>Aplicar alterações a todas as parcelas deste grupo</span>
                        </label>
                    </div>

                <?php elseif

                // modal de usuarios
                ($tipo_modal == 'usuarios'): ?>

                    <!-- conteudo do formulário -->
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="input-modal" placeholder="Nome do usuário" required>
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="input-modal" placeholder="Email do usuário" required>
                    <label for="cargo">Cargo</label>
                    <select name="cargo" id="cargo" class="input-modal">
                        <option value="0">Comum</option>
                        <option value="1">Adm</option>
                    </select>
                    <label for="senha">Senha</label>
                    <input type="password" name="senha" id="senha" class="input-modal" placeholder="Sua senha">

                <?php elseif

                // modal de avisos
                ($tipo_modal == 'avisos'): ?>

                    <!-- conteudo do formulário -->
                    <label for="titulo">Titulo</label>
                    <input type="text" name="titulo" id="titulo" class="input-modal" placeholder="Digite o titulo do aviso"
                        required>
                    <label for="conteudo">Conteudo</label>
                    <textarea type="text" name="conteudo" id="conteudo" class="input-modal" placeholder="Conteudo do aviso"
                        required></textarea>

                <?php elseif

                // modal de categorias
                ($tipo_modal == 'categorias'): ?>

                    <!-- conteudo do formulário -->
                    <label for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="input-modal" placeholder="Digite o nome da categoria"
                        required>

                <?php endif; ?>
            <?php endif; ?>
            <div class="flex items-center justify-between mt-5">
                <button type="button" class="btn-cancelar-link" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn-submit" id="btn-submit-modal">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    // funções do modal

    // Toggle groups - transforma botões em selects visuais
    document.querySelectorAll('.toggle-group').forEach(group => {
        group.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active de todos no grupo
                group.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                // Ativa o clicado
                this.classList.add('active');
                // Atualiza o hidden input correspondente
                const hiddenInput = group.previousElementSibling;
                if (hiddenInput && hiddenInput.type === 'hidden') {
                    hiddenInput.value = this.dataset.value;
                }

                // Lógica condicional: Parcelado <-> Recorrente
                if (group.id === 'toggle-parcelado') {
                    const containerRecorrente = document.getElementById('container-recorrente');
                    const containerParcelas = document.getElementById('container-parcelas');
                    if (this.dataset.value === '1') {
                        if (containerRecorrente) containerRecorrente.classList.add('hidden');
                        if (containerParcelas) containerParcelas.classList.remove('hidden');
                        // Reseta recorrente para 0
                        const recorrenteInput = document.getElementById('recorrente');
                        if (recorrenteInput) recorrenteInput.value = '0';
                    } else {
                        if (containerRecorrente) containerRecorrente.classList.remove('hidden');
                        if (containerParcelas) containerParcelas.classList.add('hidden');
                        const numParcelas = document.getElementById('num_parcelas');
                        if (numParcelas) numParcelas.value = '';
                    }
                }

                if (group.id === 'toggle-recorrente') {
                    const containerParcelado = document.getElementById('container-parcelado-wrapper');
                    const containerParcelas = document.getElementById('container-parcelas');
                    if (this.dataset.value === '1') {
                        if (containerParcelado) containerParcelado.classList.add('hidden');
                        if (containerParcelas) containerParcelas.classList.add('hidden');
                        // Reseta parcelado para 0
                        const parceladoInput = document.getElementById('parcelado');
                        if (parceladoInput) parceladoInput.value = '0';
                    } else {
                        if (containerParcelado) containerParcelado.classList.remove('hidden');
                    }
                }
            });
        });
    });

    // digitação dinamica valor
    const inputValor = document.getElementById('valor');

    inputValor.addEventListener('input', function() {
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

    // Helper: reseta todos os toggles para o valor padrão
    function resetarToggles() {
        document.querySelectorAll('.toggle-group').forEach(group => {
            group.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.value === '0') btn.classList.add('active');
            });
            const hiddenInput = group.previousElementSibling;
            if (hiddenInput && hiddenInput.type === 'hidden') {
                hiddenInput.value = '0';
            }
        });
    }

    // Helper: define o valor de um toggle group
    function setToggle(groupId, value) {
        const group = document.getElementById(groupId);
        if (!group) return;
        group.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.value === String(value)) btn.classList.add('active');
        });
        const hiddenInput = group.previousElementSibling;
        if (hiddenInput && hiddenInput.type === 'hidden') {
            hiddenInput.value = String(value);
        }
    }

    function abrirCadastrarModal(tabela) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('modal-form');
        const btnSubmit = document.getElementById('btn-submit-modal');

        modal.classList.remove('hidden');
        document.getElementById('modal-title').textContent = `Nova ${capitalizarPrimeiraLetra(tabela).replace(/s$/, '')}`;

        // limpa campos do form
        form.reset();
        resetarToggles();

        // Reseta campos de parcelas e visibilidade
        const containerParcelas = document.getElementById('container-parcelas');
        const containerEditarTodas = document.getElementById('container-editar-todas');
        const containerRecorrente = document.getElementById('container-recorrente');
        const containerParcelado = document.getElementById('container-parcelado-wrapper');
        const sectionDetalhes = document.getElementById('section-detalhes');
        const parcelaGrupoInput = document.getElementById('parcela_grupo');

        if (containerParcelas) containerParcelas.classList.add('hidden');
        if (containerEditarTodas) containerEditarTodas.classList.add('hidden');
        if (containerRecorrente) containerRecorrente.classList.remove('hidden');
        if (containerParcelado) containerParcelado.classList.remove('hidden');
        if (sectionDetalhes) sectionDetalhes.classList.remove('hidden');
        if (parcelaGrupoInput) parcelaGrupoInput.value = '';

        if (btnSubmit) btnSubmit.textContent = 'Salvar Despesa';

        // altera action do form para o PHP de cadastro
        form.action = `cadastrar_${tabela}`;
    }

    async function abrirEditarModal(tabela, id) {
        const modal = document.getElementById('modal');
        const form = document.getElementById('modal-form');
        const modalTitle = document.getElementById('modal-title');
        const btnSubmit = document.getElementById('btn-submit-modal');

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

            // Atualiza toggles com os valores
            if (dados.status !== undefined) setToggle('toggle-status', dados.status);
            if (dados.recorrente !== undefined) setToggle('toggle-recorrente', dados.recorrente);

            // Lógica de parcelas na edição
            if (tabela === 'despesas') {
                const containerParcelado = document.getElementById('container-parcelado-wrapper');
                const containerParcelas = document.getElementById('container-parcelas');
                const containerEditarTodas = document.getElementById('container-editar-todas');
                const containerRecorrente = document.getElementById('container-recorrente');

                // Esconde parcelado toggle na edição (já foi definido)
                if (containerParcelado) containerParcelado.classList.add('hidden');
                if (containerParcelas) containerParcelas.classList.add('hidden');

                // Se for despesa parcelada
                if (dados.parcela_grupo) {
                    if (containerEditarTodas) containerEditarTodas.classList.remove('hidden');
                    if (containerRecorrente) containerRecorrente.classList.add('hidden');
                    const parcelaGrupoInput = document.getElementById('parcela_grupo');
                    if (parcelaGrupoInput) parcelaGrupoInput.value = dados.parcela_grupo;
                    const editarTodas = document.getElementById('editar_todas');
                    if (editarTodas) editarTodas.checked = false;
                } else {
                    if (containerEditarTodas) containerEditarTodas.classList.add('hidden');
                    if (containerRecorrente) containerRecorrente.classList.remove('hidden');
                }

                if (btnSubmit) btnSubmit.textContent = 'Salvar Alterações';
            }

            // Atualiza título com o correto
            modalTitle.textContent = `Editar ${capitalizarPrimeiraLetra(tabela).replace(/s$/, '')}`;
        } catch (erro) {
            modalTitle.textContent = `Erro ao carregar ${capitalizarPrimeiraLetra(tabela)}`;
            console.error("Erro ao buscar dados:", erro);
        }
    }

    function fecharModal(tabela) {
        document.getElementById(`modal`).classList.add('hidden');
        // Reseta tudo ao fechar
        const containerParcelas = document.getElementById('container-parcelas');
        const containerEditarTodas = document.getElementById('container-editar-todas');
        const containerRecorrente = document.getElementById('container-recorrente');
        const containerParcelado = document.getElementById('container-parcelado-wrapper');

        if (containerParcelas) containerParcelas.classList.add('hidden');
        if (containerEditarTodas) containerEditarTodas.classList.add('hidden');
        if (containerRecorrente) containerRecorrente.classList.remove('hidden');
        if (containerParcelado) containerParcelado.classList.remove('hidden');
        resetarToggles();
    }
</script>