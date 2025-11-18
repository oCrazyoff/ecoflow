<?php
$titulo = "Avisos";
require_once "includes/layout/inicio.php";

//puxando todos os avisos
$sql = "SELECT id, titulo, conteudo, criado_em AS data FROM avisos";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// puxando todos usuarios
$sql_n_vistos = "SELECT COUNT(*) FROM usuarios";
$stmt_n_vistos = $conexao->prepare($sql_n_vistos);
$stmt_n_vistos->execute();
$stmt_n_vistos->bind_result($qtd_usuarios);
$stmt_n_vistos->fetch();
$stmt_n_vistos->close();
?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Avisos</h2>
        <div class="container-btn-tabela">
            <button onclick="abrirCadastrarModal('avisos')">
                <i class="bi bi-plus"></i>
                <span>Novo Aviso</span>
            </button>
        </div>
    </div>
    <?php if ($result->num_rows > 0) : ?>
    <div class="conteudo-tabela">
        <h3>Histórico de Avisos</h3>
        <div class="container-table">
            <table>
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Conteudo</th>
                        <th>Vistos</th>
                        <th>Não Vistos</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <?php
                            // puxando quantos vistos tem
                            $sql_vistos = "SELECT COUNT(*) FROM usuarios_avisos_vistos WHERE aviso_id = ?";
                            $stmt_vistos = $conexao->prepare($sql_vistos);
                            $stmt_vistos->bind_param("i", $row['id']);
                            $stmt_vistos->execute();
                            $stmt_vistos->bind_result($qtd_vistos);
                            $stmt_vistos->fetch();
                            $stmt_vistos->close();

                            // não vistos
                            $qtd_n_vistos = $qtd_usuarios - $qtd_vistos;
                            ?>
                    <tr>
                        <td class="font-bold"><?= htmlspecialchars($row['titulo']) ?></td>
                        <td class="truncate max-w-50">
                            <?= htmlspecialchars($row['conteudo']) ?></td>
                        <td>
                            <span class="whitespace-nowrap w-full border border-borda rounded-full px-5 py-1">
                                <i class="bi bi-eye"></i>
                                <?= htmlspecialchars($qtd_vistos) ?>
                            </span>
                        </td>
                        <td>
                            <span class="whitespace-nowrap w-full border border-borda rounded-full px-5 py-1">
                                <i class="bi bi-eye-slash"></i>
                                <?= htmlspecialchars($qtd_n_vistos) ?>
                            </span>
                        </td>
                        <td>
                            <?= htmlspecialchars(formatarData($row['data'])) ?>
                        </td>
                        <?php if ($row['id'] != 14 && $row['id'] != $_SESSION['id']): ?>
                        <td class="acoes">
                            <button class="btn-edita"
                                onclick="abrirEditarModal('avisos', <?= htmlspecialchars($row['id']) ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="deletar_avisos" method="POST">
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
        <i class="bi bi-exclamation-circle icone"></i>
        <h3 class="titulo">Nenhum aviso registrado</h3>
        <p class="paragrafo">
            Registre avisos quando precisar avisar os usuários sobre alguma manutenção ou algo importante
        </p>
        <button class="btn" onclick="abrirCadastrarModal('avisos')">
            Cadastrar Aviso
        </button>
    </div>
    <?php endif; ?>
</main>
<?php $tipo_modal = "avisos" ?>
<?php require_once "includes/modal.php" ?>
<?php require_once "includes/layout/fim.php" ?>