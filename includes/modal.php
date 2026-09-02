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
        <form id="modal-form" action="#" method="POST" data-ajax="true">
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
                                    $sql_todas_cat = "SELECT id, nome FROM categorias WHERE usuario_id = ? ORDER BY nome ASC";
                                    if ($stmt_cat = $conexao->prepare($sql_todas_cat)) {
                                        $stmt_cat->bind_param("i", $_SESSION['id']);
                                        $stmt_cat->execute();
                                        $result_cat = $stmt_cat->get_result();
                                        if ($result_cat->num_rows > 0) {
                                            while ($cat = $result_cat->fetch_assoc()) {
                                                echo "<option value='{$cat['id']}'>" . htmlspecialchars($cat['nome']) . "</option>";
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
                            <!-- Previsão do término do parcelamento (Dia, Mês e Ano) -->
                            <div id="preview-parcelas" class="hidden mt-2.5 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-950">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                        <i class="bi bi-calendar-check text-base"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 flex-wrap">
                                            <span class="text-xs uppercase font-bold tracking-wider text-emerald-700">Término do Parcelamento</span>
                                            <span id="preview-parcelas-valor" class="text-xs font-semibold text-emerald-800 bg-emerald-100/80 px-2 py-0.5 rounded"></span>
                                        </div>
                                        <div class="text-sm font-bold text-emerald-950 mt-1" id="preview-parcelas-data"></div>
                                        <div class="text-xs text-emerald-700/90 mt-0.5" id="preview-parcelas-subtexto"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campo oculto para grupo de parcelas (usado na edição) -->
                    <input type="hidden" name="parcela_grupo" id="parcela_grupo" value="">

                    <!-- Informações e Checkbox para editar todas as parcelas -->
                    <div id="container-editar-todas" class="hidden mt-3 p-3.5 bg-gradient-to-r from-amber-50/70 to-orange-50/70 rounded-lg border border-amber-200">
                        <div class="flex items-start gap-2.5 mb-2.5 pb-2.5 border-b border-amber-200/80">
                            <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 mt-0.5">
                                <i class="bi bi-layers-half text-base"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <span id="badge-parcela-edicao" class="font-bold text-xs bg-amber-200/80 text-amber-900 px-2 py-0.5 rounded"></span>
                                    <span class="text-xs text-amber-800 font-medium">Despesa Parcelada</span>
                                </div>
                                <div class="text-sm font-bold text-amber-950 mt-1" id="texto-ultima-parcela-edicao"></div>
                                <div class="text-xs text-amber-800/80 mt-0.5" id="subtexto-parcela-edicao"></div>
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer text-sm m-0 text-texto font-medium">
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
                        atualizarPreviewParcelasCadastro();
                    } else {
                        if (containerRecorrente) containerRecorrente.classList.remove('hidden');
                        if (containerParcelas) containerParcelas.classList.add('hidden');
                        const numParcelas = document.getElementById('num_parcelas');
                        if (numParcelas) numParcelas.value = '';
                        const previewParcelas = document.getElementById('preview-parcelas');
                        if (previewParcelas) previewParcelas.classList.add('hidden');
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

    const MESES_NOMES = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    /**
     * Calcula a data da última parcela baseado na data inicial e total de parcelas
     * Segue a mesma lógica do backend PHP para garantir precisão exata.
     */
    function calcularDataUltimaParcela(dataStr, totalParcelas) {
        if (!dataStr || !totalParcelas || totalParcelas < 2) return null;

        const partes = dataStr.split('-');
        if (partes.length !== 3) return null;

        const anoBase = parseInt(partes[0], 10);
        const mesBase = parseInt(partes[1], 10);
        const diaOriginal = parseInt(partes[2], 10);

        if (isNaN(anoBase) || isNaN(mesBase) || isNaN(diaOriginal)) return null;

        let mesAlvo = mesBase + (totalParcelas - 1);
        let anoAlvo = anoBase;
        while (mesAlvo > 12) {
            mesAlvo -= 12;
            anoAlvo++;
        }

        const ultimoDiaMes = new Date(anoAlvo, mesAlvo, 0).getDate();
        const diaReal = Math.min(diaOriginal, ultimoDiaMes);

        const mesFmt = String(mesAlvo).padStart(2, '0');
        const diaFmt = String(diaReal).padStart(2, '0');
        const nomeMes = MESES_NOMES[mesAlvo - 1] || '';

        return {
            dia: diaFmt,
            mes: mesFmt,
            ano: anoAlvo,
            formatada: `${diaFmt}/${mesFmt}/${anoAlvo}`,
            mesAno: `${nomeMes} de ${anoAlvo}`
        };
    }
    window.calcularDataUltimaParcela = calcularDataUltimaParcela;

    /**
     * Atualiza o card de preview da última parcela no cadastro
     */
    function atualizarPreviewParcelasCadastro() {
        const containerParcelas = document.getElementById('container-parcelas');
        const previewEl = document.getElementById('preview-parcelas');
        if (!containerParcelas || containerParcelas.classList.contains('hidden') || !previewEl) {
            if (previewEl) previewEl.classList.add('hidden');
            return;
        }

        const numParcelasInput = document.getElementById('num_parcelas');
        const dataInput = document.getElementById('data');
        const valorInput = document.getElementById('valor');

        const numParcelas = parseInt(numParcelasInput ? numParcelasInput.value : 0, 10);
        const dataStr = dataInput ? dataInput.value : '';

        if (!numParcelas || numParcelas < 2 || !dataStr) {
            previewEl.classList.add('hidden');
            return;
        }

        const infoUltima = calcularDataUltimaParcela(dataStr, numParcelas);
        if (!infoUltima) {
            previewEl.classList.add('hidden');
            return;
        }

        // Formata data inicial
        const pInicial = dataStr.split('-');
        const dataInicialFmt = pInicial.length === 3 ? `${pInicial[2]}/${pInicial[1]}/${pInicial[0]}` : dataStr;

        // Valor por parcela (se informado)
        const valorParcelaEl = document.getElementById('preview-parcelas-valor');
        if (valorParcelaEl) {
            let valStr = valorInput ? valorInput.value.replace(/\D/g, '') : '';
            let valCentavos = parseInt(valStr, 10);
            if (!isNaN(valCentavos) && valCentavos > 0) {
                let valorTotal = valCentavos / 100;
                let valorCada = (valorTotal / numParcelas).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                valorParcelaEl.textContent = `${numParcelas}x de ${valorCada}`;
            } else {
                valorParcelaEl.textContent = `${numParcelas} parcelas`;
            }
        }

        const dataEl = document.getElementById('preview-parcelas-data');
        if (dataEl) {
            dataEl.innerHTML = `<i class="bi bi-calendar-event text-emerald-600 mr-1"></i> Última parcela (${numParcelas}/${numParcelas}): <span class="underline decoration-emerald-400 font-bold">${infoUltima.formatada}</span> (${infoUltima.mesAno})`;
        }

        const subtextoEl = document.getElementById('preview-parcelas-subtexto');
        if (subtextoEl) {
            subtextoEl.textContent = `Início em ${dataInicialFmt} • Fim em ${infoUltima.formatada}`;
        }

        previewEl.classList.remove('hidden');
    }
    window.atualizarPreviewParcelasCadastro = atualizarPreviewParcelasCadastro;

    /**
     * Atualiza as informações visuais da parcela e data da última parcela na edição
     */
    function atualizarInfoParcelasEdicao(dados) {
        const badgeEl = document.getElementById('badge-parcela-edicao');
        const textoUltimaEl = document.getElementById('texto-ultima-parcela-edicao');
        const subtextoEl = document.getElementById('subtexto-parcela-edicao');

        const numAtual = parseInt(dados.parcela_numero, 10) || 1;
        const total = parseInt(dados.parcela_total, 10) || numAtual;

        if (badgeEl) {
            badgeEl.textContent = `Parcela ${numAtual} de ${total}`;
        }

        // Data da última parcela
        let dataUltimaStr = dados.parcela_ultima_data || null;

        // Se não veio pelo backend, calcula a projeção a partir da data atual
        if (!dataUltimaStr && dados.data && total > numAtual) {
            const restante = total - numAtual + 1;
            const calc = calcularDataUltimaParcela(dados.data, restante);
            if (calc) dataUltimaStr = `${calc.ano}-${calc.mes}-${calc.dia}`;
        } else if (!dataUltimaStr && dados.data && total === numAtual) {
            dataUltimaStr = dados.data;
        }

        if (textoUltimaEl) {
            if (dataUltimaStr) {
                const partes = dataUltimaStr.split('-');
                if (partes.length === 3) {
                    const diaFmt = partes[2];
                    const mesFmt = partes[1];
                    const anoFmt = partes[0];
                    const nomeMes = MESES_NOMES[parseInt(mesFmt, 10) - 1] || '';
                    const dataFormatada = `${diaFmt}/${mesFmt}/${anoFmt}`;

                    if (numAtual === total) {
                        textoUltimaEl.innerHTML = `<i class="bi bi-check-circle-fill text-amber-700 mr-1"></i> Esta é a última parcela (${total}/${total}): <span class="font-bold underline decoration-amber-400">${dataFormatada}</span> (${nomeMes} de ${anoFmt})`;
                    } else {
                        textoUltimaEl.innerHTML = `<i class="bi bi-calendar-check text-amber-700 mr-1"></i> Última parcela (${total}/${total}): <span class="font-bold underline decoration-amber-400">${dataFormatada}</span> (${nomeMes} de ${anoFmt})`;
                    }
                } else {
                    textoUltimaEl.textContent = `Última parcela: ${dataUltimaStr}`;
                }
            } else {
                textoUltimaEl.textContent = `Total de ${total} parcelas neste grupo`;
            }
        }

        if (subtextoEl) {
            if (numAtual === total) {
                subtextoEl.textContent = 'Encerramento do parcelamento deste compromisso';
            } else {
                const parcelasRestantes = total - numAtual;
                subtextoEl.textContent = `Faltam ${parcelasRestantes} ${parcelasRestantes === 1 ? 'parcela' : 'parcelas'} para quitar este compromisso`;
            }
        }
    }
    window.atualizarInfoParcelasEdicao = atualizarInfoParcelasEdicao;

    // Formatação em tempo real do campo Valor (moeda BRL)
    function formatarMoedaBRL(val) {
        if (val === null || val === undefined) return '';
        let digitos = val.toString().replace(/\D/g, '');
        if (!digitos || digitos === '0' || digitos === '00') return '';

        let centavos = parseInt(digitos, 10);
        if (isNaN(centavos) || centavos === 0) return '';

        let valorDecimal = (centavos / 100).toFixed(2);
        let partes = valorDecimal.split('.');
        let inteiro = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        let decimal = partes[1];

        return `${inteiro},${decimal}`;
    }

    function formatarValorParaInput(val) {
        if (val === null || val === undefined || val === '') return '';
        let num = parseFloat(val);
        if (isNaN(num)) return '';
        let centavos = Math.round(num * 100);
        return formatarMoedaBRL(centavos.toString());
    }

    // Delegação de evento no document para garantir que funcione sempre (mesmo se o modal for recarregado por AJAX)
    document.addEventListener('input', function(e) {
        if (e.target && (e.target.id === 'valor' || e.target.name === 'valor')) {
            e.target.value = formatarMoedaBRL(e.target.value);
        }
        if (e.target && (e.target.id === 'num_parcelas' || e.target.id === 'data' || e.target.id === 'valor')) {
            atualizarPreviewParcelasCadastro();
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target && (e.target.id === 'num_parcelas' || e.target.id === 'data')) {
            atualizarPreviewParcelasCadastro();
        }
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
        const catSelect = document.getElementById('categoria_id');
        if (catSelect) catSelect.value = '';

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

        const previewParcelas = document.getElementById('preview-parcelas');
        if (previewParcelas) previewParcelas.classList.add('hidden');

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
                if (form[campo]) {
                    if (campo === 'valor') {
                        form[campo].value = formatarValorParaInput(dados[campo]);
                    } else {
                        form[campo].value = dados[campo];
                    }
                }
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

                    // Mostra dia/mês/ano da última parcela e detalhes da parcela atual
                    atualizarInfoParcelasEdicao(dados);
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
        const previewParcelas = document.getElementById('preview-parcelas');

        if (containerParcelas) containerParcelas.classList.add('hidden');
        if (containerEditarTodas) containerEditarTodas.classList.add('hidden');
        if (containerRecorrente) containerRecorrente.classList.remove('hidden');
        if (containerParcelado) containerParcelado.classList.remove('hidden');
        if (previewParcelas) previewParcelas.classList.add('hidden');
        resetarToggles();
    }
</script>