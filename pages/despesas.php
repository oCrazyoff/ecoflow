<?php
$titulo = "Despesas";
require_once "includes/layout/inicio.php";

// Nomes dos meses para exibição
$nomesMeses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
               7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];

// puxando todas as despesas do mês e ano
if (isset($m) && $m > 0 && $m < 13) {
    $sql = "SELECT id, descricao, valor, status, recorrente, categoria_id, data, tipo, adiantamento_ref_id, data_pagamento FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['id'], $m);
} else {
    $sql = "SELECT id, descricao, valor, status, recorrente, categoria_id, data, tipo, adiantamento_ref_id, data_pagamento FROM despesas WHERE usuario_id = ? AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $_SESSION['id']);
}
$stmt->execute();
$result = $stmt->get_result();

$despesas = [];
while ($row = $result->fetch_assoc()) {
    $sql_categoria = "SELECT nome FROM categorias WHERE id = ?";
    $stmt_categoria = $conexao->prepare($sql_categoria);
    $stmt_categoria->bind_param("i", $row['categoria_id']);
    $stmt_categoria->execute();
    $stmt_categoria->bind_result($nome_categoria);
    $stmt_categoria->fetch();
    $stmt_categoria->close();
    
    $row['nome_categoria'] = $nome_categoria;
    
    // Se é adiantamento (tipo=1), buscar o mês de referência
    if ($row['tipo'] == 1 && $row['adiantamento_ref_id']) {
        $sqlRef = "SELECT MONTH(data) as mes_ref, YEAR(data) as ano_ref FROM despesas WHERE id = ?";
        $stmtRef = $conexao->prepare($sqlRef);
        $stmtRef->bind_param("i", $row['adiantamento_ref_id']);
        $stmtRef->execute();
        $stmtRef->bind_result($mesRef, $anoRef);
        $stmtRef->fetch();
        $stmtRef->close();
        $row['mes_ref_nome'] = $nomesMeses[$mesRef] ?? $mesRef;
        $row['ano_ref'] = $anoRef;
    }
    
    // Se é pago antecipadamente (status=2), buscar quando foi pago
    if ($row['status'] == 2 && $row['adiantamento_ref_id']) {
        $sqlPag = "SELECT MONTH(data) as mes_pag FROM despesas WHERE id = ?";
        $stmtPag = $conexao->prepare($sqlPag);
        $stmtPag->bind_param("i", $row['adiantamento_ref_id']);
        $stmtPag->execute();
        $stmtPag->bind_result($mesPag);
        $stmtPag->fetch();
        $stmtPag->close();
        $row['mes_pagamento_nome'] = $nomesMeses[$mesPag] ?? $mesPag;
    }
    
    $despesas[] = $row;
}
?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Despesas</h2>
        <div class="container-btn-tabela">
            <?php require_once "includes/seletor_mes.php" ?>
            <button onclick="abrirCadastrarModal('despesas')"><i class="bi bi-plus"></i>
                <span>Nova Despesa</span></button>
        </div>
    </div>
    <?php if (count($despesas) > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Despesas</h3>
            
            <!-- Mobile Cards -->
            <div class="mobile-cards md:hidden flex flex-col gap-4">
                <?php foreach ($despesas as $row) : ?>
                    <div class="bg-white border border-borda rounded-lg p-4 flex flex-col gap-3 <?= ($row['tipo'] == 1) ? 'border-l-4 border-l-purple-400' : '' ?> <?= ($row['status'] == 2) ? 'border-l-4 border-l-green-400' : '' ?>">
                        <div class="flex justify-between items-start">
                            <div class="font-bold text-lg text-texto">
                                <?php if ($row['tipo'] == 1): ?>
                                    <span class="text-purple-600">⏩</span>
                                <?php endif; ?>
                                <?= htmlspecialchars($row['descricao']) ?>
                            </div>
                            <div class="flex gap-2 text-xl">
                                <?php if ($row['status'] != 2 && $row['tipo'] != 1): ?>
                                    <button class="text-blue-500 cursor-pointer hover:bg-gray-100 rounded p-1 flex items-center justify-center"
                                        onclick="abrirEditarModal('despesas', <?= htmlspecialchars($row['id']) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="deletar_despesas" method="POST" class="m-0 flex">
                                        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                        <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">
                                        <button class="text-red-500 cursor-pointer hover:bg-gray-100 rounded p-1 flex items-center justify-center btn-deleta" type="submit">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                <?php elseif ($row['tipo'] == 1 || $row['status'] == 2): ?>
                                    <button class="text-orange-500 cursor-pointer hover:bg-gray-100 rounded p-1 flex items-center justify-center"
                                        onclick="cancelarAdiantamento(<?= $row['id'] ?>)" title="Cancelar adiantamento">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 -mt-2">
                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded"><?= htmlspecialchars($row['nome_categoria']) ?></span>
                            <span>&bull;</span>
                            <span><?= htmlspecialchars(formatarData($row['data'])) ?></span>
                            
                            <?php if ($row['tipo'] == 1): ?>
                                <span>&bull;</span>
                                <span class="bg-purple-50 text-purple-600 px-2 py-0.5 rounded flex items-center gap-1">
                                    ⏩ Adiantamento (<?= $row['mes_ref_nome'] ?? '' ?>)
                                </span>
                            <?php elseif ($row['status'] != 2): ?>
                                <span>&bull;</span>
                                <button data-id="<?= $row['id'] ?>" onclick="trocarRecorrente(this)" class="flex items-center justify-center cursor-pointer">
                                    <?php if ($row['recorrente'] == 1): ?>
                                        <span class="bg-teal-50 text-teal-600 px-2 py-0.5 rounded flex items-center gap-1 btn-recorrente-mobile">
                                            <i class="bi bi-arrow-repeat"></i> Recorrente
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded flex items-center gap-1 btn-recorrente-mobile">
                                            Não Recorrente
                                        </span>
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($row['status'] == 2): ?>
                                <span>&bull;</span>
                                <span class="bg-green-50 text-green-600 px-2 py-0.5 rounded flex items-center gap-1">
                                    ⏩ Pago antecipadamente (<?= $row['mes_pagamento_nome'] ?? '' ?>)
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <hr class="border-borda my-1">
                        
                        <div class="flex justify-between items-center mt-1">
                            <div>
                                <?php if ($row['tipo'] == 1): ?>
                                    <span class="btn-pago text-xs md:text-base">Pago</span>
                                <?php elseif ($row['status'] == 2): ?>
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs md:text-base font-medium">Pago Antecipadamente</span>
                                <?php else: ?>
                                    <button data-id="<?= $row['id'] ?>" onclick="trocarStatus(this)">
                                        <?php if ($row['status'] == 0): ?>
                                            <span class="btn-pendente text-xs md:text-base">Pendente</span>
                                        <?php else: ?>
                                            <span class="btn-pago text-xs md:text-base">Pago</span>
                                        <?php endif; ?>
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($row['recorrente'] == 1 && $row['tipo'] == 0 && $row['status'] != 2): ?>
                                    <button onclick="abrirModalAdiantamento(<?= $row['id'] ?>, '<?= htmlspecialchars($row['descricao'], ENT_QUOTES) ?>', '<?= $row['valor'] ?>')" 
                                            class="ml-2 text-purple-600 hover:text-purple-800 text-xs cursor-pointer" title="Adiantar próximo mês">
                                        <i class="bi bi-fast-forward-fill"></i> Adiantar
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="text-red-500 font-bold whitespace-nowrap text-lg">
                                - <?= htmlspecialchars(formatarReais($row['valor'])) ?>
                            </div>
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
                            <th>Status</th>
                            <th>Valor</th>
                            <th>Categoria</th>
                            <th>Data</th>
                            <th>Recorrente</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($despesas as $row) : ?>
                            <tr class="<?= ($row['tipo'] == 1) ? 'bg-purple-50/50' : '' ?> <?= ($row['status'] == 2) ? 'bg-green-50/50' : '' ?>">
                                <td class="font-bold">
                                    <?php if ($row['tipo'] == 1): ?>
                                        <span class="text-purple-600">⏩</span>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($row['descricao']) ?>
                                    <?php if ($row['tipo'] == 1): ?>
                                        <br><small class="text-purple-500 font-normal">↳ Ref. <?= $row['mes_ref_nome'] ?? '' ?>/<?= $row['ano_ref'] ?? '' ?></small>
                                    <?php elseif ($row['status'] == 2): ?>
                                        <br><small class="text-green-600 font-normal">↳ Pago em <?= $row['mes_pagamento_nome'] ?? '' ?>/<?= date('Y') ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['tipo'] == 1): ?>
                                        <span class="btn-pago">Pago</span>
                                    <?php elseif ($row['status'] == 2): ?>
                                        <span class="whitespace-nowrap bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                            Pago Antecipadamente
                                        </span>
                                    <?php else: ?>
                                        <button data-id="<?= $row['id'] ?>" onclick="trocarStatus(this)">
                                            <?php if ($row['status'] == 0): ?>
                                                <span class="btn-pendente">Pendente</span>
                                            <?php else: ?>
                                                <span class="btn-pago">Pago</span>
                                            <?php endif; ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="text-red-500 whitespace-nowrap"><?= htmlspecialchars(formatarReais($row['valor'])) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['nome_categoria']) ?>
                                </td>
                                <td><?= htmlspecialchars(formatarData($row['data'])) ?></td>
                                <td>
                                    <?php if ($row['tipo'] == 1): ?>
                                        <span class="whitespace-nowrap w-full border border-purple-200 bg-purple-50 text-purple-600 rounded-full px-5 py-1">
                                            Adiantamento
                                        </span>
                                    <?php elseif ($row['status'] == 2): ?>
                                        <span class="whitespace-nowrap w-full border border-green-200 bg-green-50 text-green-600 rounded-full px-5 py-1">
                                            Recorrente
                                        </span>
                                    <?php else: ?>
                                        <button data-id="<?= $row['id'] ?>" onclick="trocarRecorrente(this)">
                                            <?php if ($row['recorrente'] == 1): ?>
                                                <span class="whitespace-nowrap w-full border border-borda bg-teal-50 text-teal-600 rounded-full px-5 py-1 btn-recorrente-desktop">
                                                    Recorrente
                                                </span>
                                            <?php else: ?>
                                                <span class="whitespace-nowrap w-full border border-borda bg-gray-100 text-gray-500 rounded-full px-5 py-1 btn-recorrente-desktop">
                                                    Não Recorrente
                                                </span>
                                            <?php endif; ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
                                    <?php if ($row['status'] != 2 && $row['tipo'] != 1): ?>
                                        <?php if ($row['recorrente'] == 1): ?>
                                            <button class="text-purple-500 cursor-pointer hover:bg-purple-50 rounded p-1"
                                                onclick="abrirModalAdiantamento(<?= $row['id'] ?>, '<?= htmlspecialchars($row['descricao'], ENT_QUOTES) ?>', '<?= $row['valor'] ?>')"
                                                title="Adiantar próximo mês">
                                                <i class="bi bi-fast-forward-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-edita"
                                            onclick="abrirEditarModal('despesas', <?= htmlspecialchars($row['id']) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="deletar_despesas" method="POST">
                                            <!--csrf-->
                                            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                            <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">

                                            <button class="btn-deleta" type="submit"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <button class="text-orange-500 cursor-pointer hover:bg-orange-50 rounded p-1"
                                            onclick="cancelarAdiantamento(<?= $row['id'] ?>)" title="Cancelar adiantamento">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="container-mensagem">
            <i class="bi bi-wallet icone"></i>
            <h3 class="titulo">Nenhuma despesa registrada</h3>
            <p class="paragrafo">
                Registre suas despesas para melhorar seu controle financeiro
            </p>
            <button class="btn" onclick="abrirCadastrarModal('despesas')">Registrar Despesa
            </button>
        </div>
    <?php endif; ?>
</main>

<!-- Modal de Adiantamento -->
<?php require_once "includes/modal_adiantamento.php" ?>

<script>
    function trocarRecorrente(botao) {
        const id = botao.dataset.id;
        fetch("trocar_recorrente_despesa", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.sucesso) {
                    const span = botao.querySelector("span");
                    if (span.classList.contains('btn-recorrente-mobile')) {
                        if (data.novo_recorrente == 1) {
                            span.className = "bg-teal-50 text-teal-600 px-2 py-0.5 rounded flex items-center gap-1 btn-recorrente-mobile";
                            span.innerHTML = '<i class="bi bi-arrow-repeat"></i> Recorrente';
                        } else {
                            span.className = "bg-gray-100 text-gray-500 px-2 py-0.5 rounded flex items-center gap-1 btn-recorrente-mobile";
                            span.innerHTML = 'Não Recorrente';
                        }
                    } else {
                        if (data.novo_recorrente == 1) {
                            span.className = "whitespace-nowrap w-full border border-borda bg-teal-50 text-teal-600 rounded-full px-5 py-1 btn-recorrente-desktop";
                            span.textContent = "Recorrente";
                        } else {
                            span.className = "whitespace-nowrap w-full border border-borda bg-gray-100 text-gray-500 rounded-full px-5 py-1 btn-recorrente-desktop";
                            span.textContent = "Não Recorrente";
                        }
                    }
                    // Recarregar para atualizar o botão de adiantar
                    setTimeout(() => location.reload(), 300);
                }
            });
    }

    function trocarStatus(botao) {
        const id = botao.dataset.id;

        fetch("trocar_status_despesa", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id
                })
            })
            .then(resp => resp.json())
            .then(data => {

                if (data.sucesso) {
                    const span = botao.querySelector("span");

                    if (data.novo_status == 1) {
                        span.className = "btn-pago text-xs md:text-base";
                        span.textContent = "Pago";
                    } else {
                        span.className = "btn-pendente text-xs md:text-base";
                        span.textContent = "Pendente";
                    }
                }
            });
    }

    function cancelarAdiantamento(id) {
        if (!confirm('Deseja realmente cancelar este adiantamento?')) return;
        
        fetch("cancelar_adiantamento", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.sucesso) {
                location.reload();
            } else {
                alert(data.mensagem || 'Erro ao cancelar adiantamento.');
            }
        })
        .catch(() => alert('Erro de conexão. Tente novamente.'));
    }
</script>

<?php $tipo_modal = "despesas" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/layout/fim.php" ?>