<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");
// Capturar o mês selecionado na URL ou usar o mês atual como padrão
$mes_selecionado = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('n');

// Atualizar a consulta para filtrar despesas pelo mês selecionado
$sql = "SELECT * FROM despesas WHERE user_id = ? AND (MONTH(data) = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['id'], $mes_selecionado);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Despesas</title>
    <link rel="stylesheet" href="../assets/css/tabela.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../assets/css/form.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/loading.php") ?>
    <?php include("../backend/includes/menu.php") ?>
    <?php include("form/despesa.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Despesas</h2>
            <div class="btn-container">
                <?php include("../backend/includes/seletor_data.php") ?>
                <a onclick="abrirForm()" class="btn"><i class="bi bi-plus-circle"></i> Nova Despesa</a>
            </div>
        </div>
        <div class="container-table">
            <p>Histórico de Despesas</p>
            <div class="table-container">
                <table>
                    <thead>
                        <tr id="thead">
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Recorrente</th>
                            <th>Data</th>
                            <th colspan="2">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            while ($row = $result->fetch_assoc()) {
                                echo "<td>" . $row['descricao'] . "</td>";
                                echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                                echo "<td>
                                                <form action='../backend/database/despesas/atualizar_status.php' method='POST'>
                                                <input type='hidden' name='status' value='" . ($row['status'] == 1 ? 0 : 1) . "'>
                                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                <input type='hidden' name='mes' value='" . $mes_selecionado . "'>
                                                <input type='hidden' id='data' name='data' value='" . date('Y-m-d', strtotime($row['data'])) . "' required>
                                                <button type='submit' class='btn-status' " . ($row['status'] == 1 ? " id='pago'" : "") . ">" . ($row['status'] == 1 ? 'Pago' : 'Pendente') . "</button>
                                                </form>
                                                </td>";
                                echo "<td>" . ($row['recorrente'] == 1 ? 'Sim' : 'Não') . "</td>";
                                echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>";
                                echo "
                                        <td>
                                            <form method='GET'>
                                                <input type='hidden' name='editar' value='1'>
                                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                <input type='hidden' name='mes' value='" . $mes_selecionado . "'>
                                                <button type='submit' class='btn-edit'><i class='bi bi-pencil'></i></button>
                                            </form>
                                        </td>";
                                echo "
                                        <td>
                                            <form action='../backend/database/despesas/deletar.php' method='POST'>
                                                <input type='hidden' name='id' value='" . $row['id'] . "'>
                                                <input type='hidden' name='mes' value='" . $mes_selecionado . "'>
                                                <input type='hidden' name='descricao' value='" . $row['descricao'] . "'>
                                                <button type='submit' class='btn-delete'><i class='bi bi-trash'></i></button>
                                            </form>
                                        </td>";
                                echo "</tr>";
                            }
                            ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include("../backend/includes/div_erro.php") ?>
</body>

</html>