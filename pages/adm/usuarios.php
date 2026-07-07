'<?php
    $titulo = "Usuários";
    require_once "includes/layout/inicio.php";

    //puxando todos os usuarios
    $sql = "SELECT id, nome, email, cargo, ultima_verificacao FROM usuarios";
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
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
    <?php if (count($usuarios) > 0) : ?>
        <div class="conteudo-tabela">
            <h3>Histórico de Usuários</h3>

            <!-- Mobile Cards -->
            <div class="mobile-cards md:hidden flex flex-col gap-4">
                <?php foreach ($usuarios as $row) : ?>
                    <div class="bg-white border border-borda rounded-lg p-4 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-lg">
                                    <?= strtoupper(substr($row['nome'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-bold text-texto text-base"><?= htmlspecialchars($row['nome']) ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($row['email']) ?></div>
                                </div>
                            </div>
                            <div>
                                <span class="bg-blue-50 text-blue-600 text-xs px-3 py-1 rounded-full whitespace-nowrap border border-blue-100">
                                    <?= (($row['cargo'] == 0) ? 'Comum' : 'Adm') ?>
                                </span>
                            </div>
                        </div>

                        <hr class="border-borda my-1">

                        <div class="flex justify-between items-end">
                            <div>
                                <div class="text-[10px] text-gray-400 font-bold tracking-wider mb-0.5">ÚLTIMO LOGIN</div>
                                <div class="text-sm text-texto">
                                    <?php
                                    // lógica de dias atras
                                    $ultimo_login = $row['ultima_verificacao'];

                                    if ($ultimo_login) {
                                        $ultimo_ts = strtotime($ultimo_login);
                                        $hoje_ts = strtotime(date('Y-m-d'));
                                        $dias_atras = ($hoje_ts - $ultimo_ts) / 86400;
                                        $dias_atras = round($dias_atras) + 1;

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
                                        }
                                    } else {
                                        echo "Nunca acessou";
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php if ($row['id'] != 14 && $row['id'] != $_SESSION['id']): ?>
                                <div class="flex gap-2 text-lg">
                                    <button class="text-gray-500 hover:bg-gray-100 rounded p-1 cursor-pointer flex items-center justify-center"
                                        onclick="abrirEditarModal('usuarios', <?= htmlspecialchars($row['id']) ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="deletar_usuarios" method="POST" class="m-0 flex">
                                        <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                                        <input type="hidden" name="id" id="id" value="<?= $row['id'] ?>">
                                        <button class="text-gray-500 hover:bg-gray-100 rounded p-1 cursor-pointer flex items-center justify-center btn-deleta" type="submit">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Desktop Table -->
            <div class="container-table hidden md:block">
                <table>
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Último Login</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $row) : ?>
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
                        <?php endforeach; ?>
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