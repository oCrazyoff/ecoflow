<?php
$titulo = "Recorrências";
$rota = "recorrentes";
require_once "includes/layout/inicio.php";

// Buscar todos os templates do usuário
$sql = "SELECT r.*, c.nome as nome_categoria 
        FROM recorrentes r 
        LEFT JOIN categorias c ON r.categoria_id = c.id 
        WHERE r.usuario_id = ? 
        ORDER BY r.ativo DESC, r.tipo ASC, r.descricao ASC";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

$recorrentes = [];
$totalDespesasFixas = 0;
$totalRendasFixas = 0;
while ($row = $result->fetch_assoc()) {
    $recorrentes[] = $row;
    if ($row['ativo']) {
        if ($row['tipo'] === 'despesa') $totalDespesasFixas += $row['valor'];
        else $totalRendasFixas += $row['valor'];
    }
}
$stmt->close();
$saldoFixo = $totalRendasFixas - $totalDespesasFixas;
?>
<main class="main-tabela">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-borda p-4 flex flex-col justify-center">
            <span class="text-sm font-medium text-texto-opaco mb-1">Custos Fixos</span>
            <span class="text-2xl font-bold text-red-500"><?= formatarReais($totalDespesasFixas) ?></span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-borda p-4 flex flex-col justify-center">
            <span class="text-sm font-medium text-texto-opaco mb-1">Rendas Fixas</span>
            <span class="text-2xl font-bold text-green-500"><?= formatarReais($totalRendasFixas) ?></span>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-borda p-4 flex flex-col justify-center">
            <span class="text-sm font-medium text-texto-opaco mb-1">Saldo Fixo</span>
            <span class="text-2xl font-bold <?= $saldoFixo >= 0 ? 'text-blue-500' : 'text-red-500' ?>"><?= formatarReais($saldoFixo) ?></span>
        </div>
    </div>

    <!-- Header -->
    <div class="header-tabela">
        <h2>Recorrências</h2>
        <div class="container-btn-tabela">
            <button onclick="abrirCriarRecorrente()"><i class="bi bi-plus"></i>
                <span>Nova Recorrência</span></button>
        </div>
    </div>

    <?php if (empty($recorrentes)): ?>
        <div class="container-mensagem">
            <i class="bi bi-arrow-repeat icone"></i>
            <h3 class="titulo">Nenhuma recorrência cadastrada</h3>
            <p class="paragrafo">Cadastre suas contas recorrentes (aluguel, streaming, salário) para
                que o sistema registre tudo automaticamente.</p>
            <button class="btn" onclick="abrirCriarRecorrente()">Nova Recorrência</button>
        </div>
    <?php else: ?>
        <div class="conteudo-tabela">
            <h3>Custos e Rendas Fixas</h3>

            <!-- Mobile Cards -->
            <div class="mobile-cards md:hidden flex flex-col gap-4">
                <?php foreach ($recorrentes as $rec): 
                    $tipo_color = $rec['tipo'] === 'despesa' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600';
                    $valor_color = $rec['tipo'] === 'despesa' ? 'text-red-500' : 'text-green-500';
                    $ativo_class = $rec['ativo'] ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500';
                ?>
                    <div class="bg-white border border-borda rounded-lg p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div class="font-bold text-lg text-texto"><?= htmlspecialchars($rec['descricao']) ?></div>
                            <div class="<?= $valor_color ?> font-bold whitespace-nowrap text-lg">
                                <?= formatarReais($rec['valor']) ?>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 -mt-2">
                            <span class="<?= $tipo_color ?> px-2 py-0.5 rounded text-xs">
                                <?= $rec['tipo'] === 'despesa' ? 'Despesa' : 'Renda' ?>
                            </span>
                            <?php if ($rec['nome_categoria']): ?>
                                <span>&bull;</span>
                                <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded text-xs"><?= htmlspecialchars($rec['nome_categoria']) ?></span>
                            <?php endif; ?>
                            <span>&bull;</span>
                            <span>Dia <?= htmlspecialchars($rec['dia_vencimento']) ?></span>
                            <span>&bull;</span>
                            <button data-id="<?= $rec['id'] ?>" onclick="pausarRecorrente(this)" class="cursor-pointer">
                                <span class="<?= $ativo_class ?> px-2 py-0.5 rounded text-xs flex items-center gap-1">
                                    <i class="bi <?= $rec['ativo'] ? 'bi-play-circle' : 'bi-pause-circle' ?>"></i>
                                    <?= $rec['ativo'] ? 'Ativo' : 'Pausado' ?>
                                </span>
                            </button>
                        </div>

                        <div class="text-xs text-texto-opaco -mt-1">
                            Desde <?= formatarData($rec['data_inicio']) ?>
                            <?php if ($rec['data_fim']): ?>
                                &bull; Até <?= formatarData($rec['data_fim']) ?>
                            <?php endif; ?>
                        </div>

                        <hr class="border-borda my-1">

                        <div class="flex justify-end gap-3 text-sm">
                            <button class="text-gray-600 flex items-center gap-1 cursor-pointer hover:text-blue-500 font-medium"
                                onclick="abrirEditarRecorrente(<?= $rec['id'] ?>)">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <form action="deletar_recorrente" method="POST" class="m-0 flex">
                                <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                                <button class="text-gray-600 flex items-center gap-1 cursor-pointer hover:text-red-500 font-medium btn-deleta" type="submit">
                                    <i class="bi bi-trash3"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table -->
            <div class="container-table hidden md:block">
                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Valor</th>
                            <th>Dia</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recorrentes as $rec): 
                            $tipo_color = $rec['tipo'] === 'despesa' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600';
                            $valor_color = $rec['tipo'] === 'despesa' ? 'text-red-500' : 'text-green-500';
                            $ativo_class = $rec['ativo'] ? 'bg-green-50 text-green-600' : 'bg-gray-100 text-gray-500';
                        ?>
                            <tr>
                                <td class="font-bold">
                                    <?= htmlspecialchars($rec['descricao']) ?>
                                    <div class="text-xs text-gray-400 font-normal">
                                        Desde <?= formatarData($rec['data_inicio']) ?>
                                        <?= $rec['data_fim'] ? ' até ' . formatarData($rec['data_fim']) : '' ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="<?= $tipo_color ?> px-2.5 py-1 rounded-md text-xs font-medium whitespace-nowrap">
                                        <?= $rec['tipo'] === 'despesa' ? 'Despesa' : 'Renda' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($rec['nome_categoria']): ?>
                                        <?= htmlspecialchars($rec['nome_categoria']) ?>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="<?= $valor_color ?> whitespace-nowrap">
                                    <?= formatarReais($rec['valor']) ?>
                                </td>
                                <td class="text-center text-gray-600">
                                    <?= htmlspecialchars($rec['dia_vencimento']) ?>
                                </td>
                                <td>
                                    <button data-id="<?= $rec['id'] ?>" onclick="pausarRecorrente(this)" class="cursor-pointer">
                                        <span class="<?= $ativo_class ?> px-3 py-1 rounded-full text-xs font-medium whitespace-nowrap">
                                            <i class="bi <?= $rec['ativo'] ? 'bi-play-circle' : 'bi-pause-circle' ?>"></i>
                                            <?= $rec['ativo'] ? 'Ativo' : 'Pausado' ?>
                                        </span>
                                    </button>
                                </td>
                                <td class="acoes">
                                    <button class="btn-edita" onclick="abrirEditarRecorrente(<?= $rec['id'] ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="deletar_recorrente" method="POST">
                                        <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
                                        <input type="hidden" name="id" value="<?= $rec['id'] ?>">
                                        <button class="btn-deleta" type="submit"><i class="bi bi-trash3"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
    // Pausar/reativar recorrência
    function pausarRecorrente(botao) {
        const id = botao.dataset.id;
        fetch("pausar_recorrente", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.sucesso) {
                location.reload();
            }
        });
    }

    // Funções do modal de criar/editar recorrência
    function abrirCriarRecorrente() {
        document.getElementById('modal').classList.remove('hidden');
        document.getElementById('modal-form').reset();
        document.getElementById('modal-title').textContent = 'Nova Recorrência';
        document.getElementById('modal-form').action = 'criar_recorrente';
        document.getElementById('campo-categoria').classList.remove('hidden');
    }

    function abrirEditarRecorrente(id) {
        fetch('buscar_recorrente?id=' + id)
            .then(resp => resp.json())
            .then(data => {
                if (data.erro) return alert(data.erro);
                document.getElementById('modal').classList.remove('hidden');
                document.getElementById('modal-title').textContent = 'Editar Recorrência';
                document.getElementById('modal-form').action = 'editar_recorrente?id=' + id;
                document.getElementById('rec-tipo').value = data.tipo;
                document.getElementById('rec-descricao').value = data.descricao;
                document.getElementById('rec-valor').value = parseFloat(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('rec-dia').value = data.dia_vencimento;
                document.getElementById('rec-data-inicio').value = data.data_inicio;
                document.getElementById('rec-data-fim').value = data.data_fim || '';
                if (data.tipo === 'despesa') {
                    document.getElementById('campo-categoria').classList.remove('hidden');
                    document.getElementById('rec-categoria').value = data.categoria_id || '';
                } else {
                    document.getElementById('campo-categoria').classList.add('hidden');
                }
            });
    }

    function fecharModalRecorrente() {
        document.getElementById('modal').classList.add('hidden');
    }

    function toggleCategoria() {
        const tipo = document.getElementById('rec-tipo').value;
        const campo = document.getElementById('campo-categoria');
        if (tipo === 'despesa') {
            campo.classList.remove('hidden');
        } else {
            campo.classList.add('hidden');
        }
    }

</script>

<!-- Modal de Recorrência -->
<div id="modal" class="hidden">
    <div id="form-container">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-arrow-repeat text-teal-600 text-lg"></i>
                </div>
                <h2 id="modal-title" class="text-xl font-bold text-texto">Nova Recorrência</h2>
            </div>
            <button type="button" onclick="fecharModalRecorrente()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none cursor-pointer" aria-label="Fechar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="modal-form" action="criar_recorrente" method="POST">
            <input type="hidden" name="csrf" value="<?= gerarCSRF() ?>">
            
            <!-- Tipo -->
            <label for="rec-tipo">Tipo</label>
            <select id="rec-tipo" name="tipo" onchange="toggleCategoria()" required class="input-modal">
                <option value="despesa">Despesa</option>
                <option value="renda">Renda</option>
            </select>

            <!-- Descrição -->
            <label for="rec-descricao">Descrição</label>
            <input id="rec-descricao" name="descricao" type="text" required maxlength="255"
                class="input-modal" placeholder="Ex: Aluguel, Netflix, Salário...">

            <!-- Valor -->
            <label for="rec-valor">Valor</label>
            <input id="rec-valor" name="valor" type="text" required
                class="input-modal" placeholder="0,00">

            <!-- Categoria (só para despesas) -->
            <div id="campo-categoria">
                <label for="rec-categoria">Categoria</label>
                <select id="rec-categoria" name="categoria_id" class="input-modal">
                    <option value="">Selecione...</option>
                    <?php
                    $sqlCat = "SELECT id, nome FROM categorias WHERE usuario_id = ? ORDER BY nome ASC";
                    $stmtCat = $conexao->prepare($sqlCat);
                    $stmtCat->bind_param('i', $_SESSION['id']);
                    $stmtCat->execute();
                    $resCat = $stmtCat->get_result();
                    while ($cat = $resCat->fetch_assoc()): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nome']) ?></option>
                    <?php endwhile;
                    $stmtCat->close(); ?>
                </select>
            </div>

            <!-- Dia do vencimento -->
            <label for="rec-dia">Dia do vencimento</label>
            <input id="rec-dia" name="dia_vencimento" type="number" min="1" max="31" required
                class="input-modal" value="<?= date('d') ?>">

            <!-- Data início -->
            <label for="rec-data-inicio">Data de início</label>
            <input id="rec-data-inicio" name="data_inicio" type="date" required
                class="input-modal" value="<?= date('Y-m-d') ?>">

            <!-- Data fim (opcional) -->
            <label for="rec-data-fim">Data de fim <span class="text-texto-opaco font-normal">(opcional)</span></label>
            <input id="rec-data-fim" name="data_fim" type="date" class="input-modal">

            <!-- Botões -->
            <div class="flex items-center justify-between mt-5">
                <button type="button" class="btn-cancelar-link" onclick="fecharModalRecorrente()">Cancelar</button>
                <button type="submit" class="btn-submit" id="btn-submit-modal">Salvar</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('modal').addEventListener('click', function(e) {
        if (e.target === this) fecharModalRecorrente();
    });
</script>

<?php require_once "includes/layout/fim.php" ?>
