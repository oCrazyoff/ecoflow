<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");
// Capturar o mês selecionado na URL ou usar o mês atual como padrão
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n');

// Atualizar a consulta para filtrar rendas pelo mês selecionado
$sql = "SELECT * FROM rendas WHERE user_id = ? AND (MONTH(data) = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['id'], $selectedMonth);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Rendas</title>
    <link rel="stylesheet" href="../assets/css/tabela.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/loading.php") ?>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Rendas</h2>
            <div class="btn-container">
                <?php include("../backend/includes/seletor_data.php") ?>
                <a href="cadastro/renda.php" class="btn"><i class="bi bi-plus-circle"></i> Nova Renda</a>
            </div>
        </div>
        <div class="container-table">
            <p>Histórico de Rendas</p>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Recorrente</th>
                            <th>Data</th>
                            <th colspan="2">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['descricao']) . "</td>";
                            echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                            echo "<td>" . $row['recorrente'] . "</td>";
                            echo "<td>" . date('d/m/Y', strtotime($row['data'])) . "</td>"; // Exibir a data formatada
                            echo "
                        <td>
                            <form action='editar/renda.php' method='POST'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                <button type='submit' class='btn-edit'><i class='bi bi-pencil'></i></button>
                            </form>
                        </td>";
                            echo "
                        <td>
                            <form action='../backend/database/rendas/deletar.php' method='POST'>
                                <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
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