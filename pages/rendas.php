<?php
$titulo = "Rendas";
require_once "includes/layout/inicio.php";

//puxando todas as rendas do mês e ano
if (isset($m) && $m > 0 && $m < 13) {
    $sql = "SELECT id, descricao, valor, recorrente, data FROM rendas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['id'], $m);
} else {
    $sql = "SELECT id, descricao, valor, recorrente, data FROM rendas WHERE usuario_id = ? AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $_SESSION['id']);
}
$stmt->execute();
$result = $stmt->get_result();

$rendas = [];
while ($row = $result->fetch_assoc()) {
    $rendas[] = $row;
}
?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Rendas</h2>
        <div class="container-btn-tabela">
            <?php require_once "includes/seletor_mes.php" ?>
            <button onclick="abrirCadastrarModal('rendas')"><i class="bi bi-plus"></i> <span>Nova Renda</span></button>
        </div>
    </div>
    <?php if (count($rendas) > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Rendas</h3>

            <!-- Mobile Cards -->
            <div class="mobile-cards md:hidden flex flex-col gap-4">
                <?php foreach ($rendas as $row) : ?>
                    <div class="bg-white border border-borda rounded-lg p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div class="font-bold text-lg text-texto"><?= htmlspecialchars($row['descricao']) ?></div>
                            <div class="text-green-500 font-bold whitespace-nowrap"><?= htmlspecialchars(formatarReais($row['valor'])) ?></div>
                        </div>
                        <div class="text-gray-500 text-sm -mt-2">
                            <?= htmlspecialchars(formatarData($row['data'])) ?>
                        </div>
                        <div>
                            <button data-id="<?= $row['id'] ?>" onclick="trocarRecorrente(this)" class="flex items-center justify-center cursor-pointer">
                                <?php if ($row['recorrente'] == 1): ?>
                                    <span class="bg-teal-50 text-teal-600 px-3 py-1 text-xs rounded-full flex items-center gap-1 btn-recorrente-mobile">
                                        <i class="bi bi-arrow-repeat"></i> Recorrente
                                    </span>
                                <?php else: ?>
                                    <span class="bg-gray-100 text-gray-500 px-3 py-1 text-xs rounded-full flex items-center gap-1 btn-recorrente-mobile">
                                        Não Recorrente
                                    </span>
                                <?php endif; ?>
                            </button>
                        </div>
                        
                        <hr class="border-borda my-1">
                        
                        <div class="flex justify-end gap-3 text-sm">
                            <button class="text-gray-600 flex items-center gap-1 cursor-pointer hover:text-blue-500 font-medium"
                                onclick="abrirEditarModal('rendas', <?= htmlspecialchars($row['id']) ?>)">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <form action="deletar_rendas" method="POST" class="m-0 flex">
                                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">
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
                            <th>Valor</th>
                            <th>Recorrente</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rendas as $row) : ?>
                            <tr>
                                <td class="font-bold"><?= htmlspecialchars($row['descricao']) ?></td>
                                <td class="text-green-500 whitespace-nowrap">
                                    <?= htmlspecialchars(formatarReais($row['valor'])) ?></td>
                                <td>
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
                                </td>
                                <td><?= htmlspecialchars(formatarData($row['data'])) ?></td>
                                <td class="acoes">
                                    <button class="btn-edita"
                                        onclick="abrirEditarModal('rendas', <?= htmlspecialchars($row['id']) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="deletar_rendas" method="POST">
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
            <i class="bi bi-cash-stack icone"></i>
            <h3 class="titulo">Nenhuma renda registrada</h3>
            <p class="paragrafo">Comece a registrar suas rendas para ter um controle
                financeiro completo</p>
            <button class="btn" onclick="abrirCadastrarModal('rendas')">Registrar Renda
            </button>
        </div>
    <?php endif; ?>
</main>
<script>
    function trocarRecorrente(botao) {
        const id = botao.dataset.id;
        fetch("trocar_recorrente_renda", {
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
                            span.className = "bg-teal-50 text-teal-600 px-3 py-1 text-xs rounded-full flex items-center gap-1 btn-recorrente-mobile";
                            span.innerHTML = '<i class="bi bi-arrow-repeat"></i> Recorrente';
                        } else {
                            span.className = "bg-gray-100 text-gray-500 px-3 py-1 text-xs rounded-full flex items-center gap-1 btn-recorrente-mobile";
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
</script>
<?php $tipo_modal = "rendas" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/layout/fim.php" ?>