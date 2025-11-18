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
                                <td>
                                    <?php
                                    // lógica de dias atras
                                    $ultimo_login = $row['ultima_verificacao'];

                                    // transforma em timestamp
                                    $ultimo_ts = strtotime($ultimo_login);

                                    // timestamp de hoje (0h)
                                    $hoje_ts = strtotime(date('Y-m-d'));

                                    // diferença em dias
                                    $dias_atras = ($hoje_ts - $ultimo_ts) / 86400; // 86400 = segundos de 1 dia
                                    $dias_atras = round($dias_atras) + 1; // arredondando

                                    if ($dias_atras <= 30) {

                                        if ($dias_atras == 0) {
                                            echo "Hoje";
                                        } elseif ($dias_atras == 1) {
                                            echo "Ontem";
                                        } else {
                                            echo intval($dias_atras) . " dias atrás";
                                        }
                                    } elseif ($dias_atras >= 31 && $dias_atras <= 365) {

                                        $meses_atras = floor($dias_atras / 30);

                                        echo $meses_atras . ($meses_atras == 1 ? " mês atrás" : " meses atrás");
                                    } elseif ($dias_atras > 365) {

                                        echo "Ano passado";
                                    } else {
                                        echo "N/A";
                                    }
                                    ?>
                                </td>
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