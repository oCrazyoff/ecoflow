<?php
$titulo = "Despesas";
require_once "includes/inicio.php";

// puxando todas as despesas do mês e ano
if (isset($m) && $m > 0 && $m < 13) {
    $sql = "SELECT id, descricao, valor, status, recorrente, categoria, data FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('ii', $_SESSION['id'], $m);
} else {
    $sql = "SELECT id, descricao, valor, status, recorrente, categoria, data FROM despesas WHERE usuario_id = ? AND MONTH(data) = MONTH(CURDATE()) AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param('i', $_SESSION['id']);
}
$stmt->execute();
$result = $stmt->get_result();
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
    <?php if ($result->num_rows > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Despesas</h3>
            <div class="container-table">
                <table>
                    <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Recorrente</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td class="font-bold"><?= htmlspecialchars($row['descricao']) ?></td>
                            <td><?= htmlspecialchars(tipoCategorias($row['categoria'])) ?></td>
                            <td class="text-red-500 whitespace-nowrap"><?= htmlspecialchars(formatarReais($row['valor'])) ?></td>
                            <td>
                                <?php if ($row['status'] == 0): ?>
                                    <span class="w-full bg-yellow-500 text-white rounded-full px-5 py-1">
                                        Pendente
                                    </span>
                                <?php else: ?>
                                    <span class="w-full bg-green-500 text-white rounded-full px-5 py-1">
                                        Pago
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(formatarData($row['data'])) ?></td>
                            <td>
                                <span class="whitespace-nowrap w-full border border-borda rounded-full px-5 py-1">
                                <?= (($row['recorrente'] == 0) ? 'Não Recorrente' : 'Recorrente') ?>
                                </span>
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
                    <?php endwhile; ?>
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
            <button class="btn"
                    onclick="abrirCadastrarModal('despesas')">Registrar Despesa
            </button>
        </div>
    <?php endif; ?>
</main>
<?php $tipo_modal = "despesas" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/fim.php" ?>
