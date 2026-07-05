<?php
$titulo = "Despesas";
require_once "includes/layout/inicio.php";

// puxando todas as despesas do mês e ano
if (isset($m) && $m > 0 && $m < 13) {
    $sql = "SELECT id, descricao, valor, status, recorrente, categoria_id, data, parcela_numero, parcela_total FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['id'], $m);
} else {
    $sql = "SELECT id, descricao, valor, status, recorrente, categoria_id, data, parcela_numero, parcela_total FROM despesas WHERE usuario_id = ? AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())";
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
                    <div class="bg-white border border-borda rounded-lg p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div class="font-bold text-lg text-texto"><?= htmlspecialchars($row['descricao']) ?></div>
                            <div class="flex gap-2 text-xl">
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
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 -mt-2">
                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded"><?= htmlspecialchars($row['nome_categoria']) ?></span>
                            <span>&bull;</span>
                            <span><?= htmlspecialchars(formatarData($row['data'])) ?></span>
                            <span>&bull;</span>
                            <?php if (!empty($row['parcela_numero']) && !empty($row['parcela_total'])): ?>
                                <span class="bg-purple-50 text-purple-600 px-2 py-0.5 rounded flex items-center gap-1">
                                    <i class="bi bi-layers"></i> Parcelada (<?= $row['parcela_numero'] ?>/<?= $row['parcela_total'] ?>)
                                </span>
                            <?php else: ?>
                                <button data-id="<?= $row['id'] ?>" onclick="trocarRecorrente(this)" class="flex items-center justify-center cursor-pointer">
                                    <?php if ($row['recorrente'] == 1): ?>
                                        <span class="bg-teal-50 text-teal-600 px-2 py-0.5 rounded flex items-center gap-1 btn-recorrente-mobile">
                                            <i class="bi bi-arrow-repeat"></i> Recorrente
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded flex items-center gap-1 btn-recorrente-mobile">
                                            Única
                                        </span>
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <hr class="border-borda my-1">
                        
                        <div class="flex justify-between items-center mt-1">
                            <div>
                                <button data-id="<?= $row['id'] ?>" onclick="trocarStatus(this)">
                                    <?php if ($row['status'] == 0): ?>
                                        <span class="btn-pendente text-xs md:text-base">Pendente</span>
                                    <?php else: ?>
                                        <span class="btn-pago text-xs md:text-base">Pago</span>
                                    <?php endif; ?>
                                </button>
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
                            <th>Tipo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($despesas as $row) : ?>
                            <tr>
                                <td class="font-bold"><?= htmlspecialchars($row['descricao']) ?></td>
                                <td>
                                    <button data-id="<?= $row['id'] ?>" onclick="trocarStatus(this)">
                                        <?php if ($row['status'] == 0): ?>
                                            <span class="btn-pendente">
                                                Pendente
                                            </span>
                                        <?php else: ?>
                                            <span class="btn-pago">
                                                Pago
                                            </span>
                                        <?php endif; ?>
                                    </button>
                                </td>
                                <td class="text-red-500 whitespace-nowrap"><?= htmlspecialchars(formatarReais($row['valor'])) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['nome_categoria']) ?>
                                </td>
                                <td><?= htmlspecialchars(formatarData($row['data'])) ?></td>
                                <td>
                                    <?php if (!empty($row['parcela_numero']) && !empty($row['parcela_total'])): ?>
                                        <span class="whitespace-nowrap w-full border border-borda bg-purple-50 text-purple-600 rounded-full px-5 py-1">
                                            <i class="bi bi-layers"></i> Parcelada (<?= $row['parcela_numero'] ?>/<?= $row['parcela_total'] ?>)
                                        </span>
                                    <?php else: ?>
                                        <button data-id="<?= $row['id'] ?>" onclick="trocarRecorrente(this)">
                                            <?php if ($row['recorrente'] == 1): ?>
                                                <span class="whitespace-nowrap w-full border border-borda bg-teal-50 text-teal-600 rounded-full px-5 py-1 btn-recorrente-desktop">
                                                    Recorrente
                                                </span>
                                            <?php else: ?>
                                                <span class="whitespace-nowrap w-full border border-borda bg-gray-100 text-gray-500 rounded-full px-5 py-1 btn-recorrente-desktop">
                                                    Única
                                                </span>
                                            <?php endif; ?>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="acoes">
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
</script>

<?php $tipo_modal = "despesas" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/layout/fim.php" ?>