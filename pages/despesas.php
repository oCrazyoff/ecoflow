<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$sql = "SELECT * FROM despesas WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Despesas</title>
    <link rel="stylesheet" href="../assets/css/tabela.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Despesas</h2>
            <a href="cadastro/despesa.php" class="btn"><i class="bi bi-plus-circle"></i> Nova Despesa</a>
        </div>
        <div class="table-container">
            <table>
                <tr>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Frequencia</th>
                    <th>Tipo</th>
                    <th colspan="2">Ações</th>
                </tr>
                <tr>
                    <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<td>" . $row['descricao'] . "</td>";
                    echo "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
                    echo "<td>" . $row['frequencia'] . "</td>";
                    echo "<td>" . $row['tipo'] . "</td>";
                    echo "
                    <td>
                        <form action='editar/despesa.php' method='POST'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' class='btn-delete'><i class='bi bi-pencil'></i></button>
                        </form>
                    </td>";
                    echo "
                    <td>
                        <form action='../backend/database/despesas/deletar.php' method='POST'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' class='btn-delete'><i class='bi bi-trash'></i></button>
                        </form>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <script>
    <?php
        if (isset($_SESSION['resposta'])) {
            echo "alert('" . $_SESSION['resposta'] . "');";
            unset($_SESSION['resposta']);
        }
        ?>
    </script>
</body>

</html>