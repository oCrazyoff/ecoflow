<?php
$titulo = "Rendas";
require_once "includes/inicio.php"
?>
<main class="main-tabela">
    <div class="header-tabela">
        <h2>Rendas</h2>
        <div class="container-btn-tabela">
            <?php require_once "includes/seletor_mes.php" ?>
            <button><i class="bi bi-plus-circle"></i> Nova Renda</button>
        </div>
    </div>
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
                <?php
                // puxando todas as rendas do mês
                $m = $_GET['m'] ?? NULL;

                if (isset($m) && $m > 0 && $m < 13) {
                    $sql = "SELECT descricao, valor, recorrente, data FROM rendas WHERE usuario_id = ? AND MONTH(data) = ?";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param('ii', $_SESSION['id'], $m);
                } else {
                    $sql = "SELECT descricao, valor, recorrente, data FROM rendas WHERE usuario_id = ? AND MONTH(data) = MONTH(CURDATE())";
                    $stmt = $conexao->prepare($sql);
                    $stmt->bind_param('i', $_SESSION['id']);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) :
                    while ($row = $result->fetch_assoc()) :
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['descricao']) ?></td>
                            <td><?= htmlspecialchars($row['valor']) ?></td>
                            <td><?= htmlspecialchars($row['recorrente']) ?></td>
                            <td><?= htmlspecialchars($row['data']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: $_SESSION['resposta'] = "Sem registros!"; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once "includes/fim.php" ?>
