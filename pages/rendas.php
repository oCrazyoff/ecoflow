<?php
$titulo = "Rendas";
require_once "includes/inicio.php";

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
?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Rendas</h2>
        <div class="container-btn-tabela">
            <?php require_once "includes/seletor_mes.php" ?>
            <button onclick="abrirCadastrarModal('rendas')"><i class="bi bi-plus"></i> <span>Nova Renda</span></button>
        </div>
    </div>
    <?php if ($result->num_rows > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Rendas</h3>
            <div class="container-table">
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
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <tr>
                            <td class="font-bold"><?= htmlspecialchars($row['descricao']) ?></td>
                            <td class="text-green-500 whitespace-nowrap"><?= htmlspecialchars(formatarReais($row['valor'])) ?></td>
                            <td>
                                <span class="whitespace-nowrap w-full border border-borda rounded-full px-5 py-1">
                                <?= (($row['recorrente'] == 0) ? 'Não Recorrente' : 'Recorrente') ?>
                                </span>
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
                    <?php endwhile; ?>
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
            <button class="btn"
                    onclick="abrirCadastrarModal('rendas')">Registrar Renda
            </button>
        </div>
    <?php endif; ?>
</main>
<?php $tipo_modal = "rendas" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/fim.php" ?>
