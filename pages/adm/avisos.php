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
                                <td class="acoes">
                                    <button id="btn-visu-<?= $row['id'] ?>" class="btn-visu"
                                        onclick="abrirVisualizar('<?= $row['id'] ?>')">
                                        <i class="bi bi-eye"></i>
                                    </button>
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
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        $sql_avisos = "SELECT id, titulo FROM avisos";
        $stmt_avisos = $conexao->prepare($sql_avisos);
        $stmt_avisos->execute();
        $result_avisos = $stmt_avisos->get_result();
        $stmt_avisos->close();

        if ($result_avisos->num_rows > 0):
            while ($row_avisos = $result_avisos->fetch_assoc()):
        ?>
                <div class="modal-visualizar hidden" id="visualizar-<?= $row_avisos['id'] ?>">
                    <div class="container-modal">
                        <button class="btn-fechar" onclick="fecharVisualizar('<?= $row_avisos['id'] ?>')">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="titulo">
                            <h2>Status de Visualização - <?= $row_avisos['titulo'] ?></h2>
                            <p>Veja quais usuários já visualizaram este aviso</p>
                        </div>
                        <div class="container-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // buscando os usuarios e vendo se visualizaram ou não
                                    $sql_users = "SELECT id, nome FROM usuarios";
                                    $stmt_users = $conexao->prepare($sql_users);
                                    $stmt_users->execute();
                                    $result_users = $stmt_users->get_result();
                                    $stmt_users->close();

                                    while ($row_user = $result_users->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row_user['nome']) ?></td>
                                            <?php
                                            $sql_aviso = "SELECT usuario_id FROM usuarios_avisos_vistos WHERE aviso_id = ?";
                                            $stmt_aviso = $conexao->prepare($sql_aviso);
                                            $stmt_aviso->bind_param("s", $row_avisos['id']);
                                            $stmt_aviso->execute();
                                            $result_aviso = $stmt_aviso->get_result();
                                            $stmt_aviso->close();

                                            $usuarios_viram = [];

                                            while ($row_aviso = $result_aviso->fetch_assoc()) {
                                                $usuarios_viram[] = $row_aviso['usuario_id'];
                                            }

                                            if (in_array($row_user['id'], $usuarios_viram)):
                                            ?>
                                                <td class="tag-visu">
                                                    <p>
                                                        <i class="bi bi-eye"></i> Visualizado
                                                    </p>
                                                </td>
                                            <?php else: ?>
                                                <td class="tag-n-visu">
                                                    <p>
                                                        <i class="bi bi-eye-slash"></i> Não Visualizado
                                                    </p>
                                                </td>
                                            <?php endif ?>
                                        <?php endwhile ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endwhile ?>
            <script>
                function abrirVisualizar(aviso) {
                    const visualizar = document.getElementById("visualizar-" + aviso);

                    visualizar.classList.remove("hidden");
                }

                function fecharVisualizar(aviso) {
                    const visualizar = document.getElementById("visualizar-" + aviso);

                    visualizar.classList.add("hidden");
                }
            </script>
        <?php endif ?>
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