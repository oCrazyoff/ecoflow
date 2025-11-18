<?php
$titulo = "Usuários";
require_once "includes/layout/inicio.php";

//puxando todos os usuarios
$sql = "SELECT id, nome, email, cargo, ultima_verificacao, relatorio_anual_pendente FROM usuarios";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Usuários</h2>
        <div class="container-btn-tabela">
            <button onclick="abrirCadastrarModal('usuarios')">
                <i class="bi bi-plus"></i>
                <span>Novo Usuário</span>
            </button>
        </div>
    </div>
    <?php if ($result->num_rows > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Usuários</h3>
            <div class="container-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Último Login</th>
                            <th>Relatório</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td class="font-bold"><?= htmlspecialchars($row['nome']) ?></td>
                                <td class="whitespace-nowrap">
                                    <?= htmlspecialchars($row['email']) ?></td>
                                <td>
                                    <span class="whitespace-nowrap w-full border border-borda rounded-full px-5 py-1">
                                        <?= (($row['cargo'] == 0) ? 'Comum' : 'Adm') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(formatarData($row['ultima_verificacao'])) ?></td>
                                <td>
                                    <?= htmlspecialchars(($row['relatorio_anual_pendente'] == 0) ? 'Não' : 'Sim') ?>
                                </td>
                                <?php if ($row['id'] != 14 && $row['id'] != $_SESSION['id']): ?>
                                    <td class="acoes">
                                        <button class="btn-edita"
                                            onclick="abrirEditarModal('usuarios', <?= htmlspecialchars($row['id']) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="deletar_usuarios" method="POST">
                                            <!--csrf-->
                                            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                            <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">

                                            <button class="btn-deleta" type="submit"><i class="bi bi-trash3"></i></button>
                                        </form>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="container-mensagem">
            <i class="bi bi-people icone"></i>
            <h3 class="titulo">Nenhum usuário registrado</h3>
            <p class="paragrafo">
                Registre um usuário ao sistema EcoFlow
            </p>
            <button class="btn" onclick="abrirCadastrarModal('usuarios')">
                Registrar Usuário
            </button>
        </div>
    <?php endif; ?>
</main>
<?php $tipo_modal = "usuarios" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/layout/fim.php" ?>