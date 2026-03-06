<?php
$titulo = "Categorias";
require_once "includes/layout/inicio.php";

//puxando todas as categorias do usuário
$sql = "SELECT id, nome FROM categorias WHERE usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Categorias</h2>
        <div class="container-btn-tabela">
            <button onclick="abrirCadastrarModal('categorias')">
                <i class="bi bi-plus"></i> <span>Nova Categoria</span>
            </button>
        </div>
    </div>
    <?php if ($result->num_rows > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Categorias</h3>
            <div class="container-table">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td class="font-bold"><?= htmlspecialchars($row['nome']) ?></td>
                                <td class="acoes">
                                    <button class="btn-edita"
                                        onclick="abrirEditarModal('categorias', <?= htmlspecialchars($row['id']) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="deletar_categorias" method="POST">
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
            <i class="bi bi-tags"></i>
            <h3 class="titulo">Nenhuma categoria registrada</h3>
            <p class="paragrafo">
                Comece a registrar suas categorias para ter um controle
                financeiro completo
            </p>
            <button class="btn" onclick="abrirCadastrarModal('categorias')">Registrar Categoria
            </button>
        </div>
    <?php endif; ?>
</main>
<?php $tipo_modal = "categorias" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/layout/fim.php" ?>