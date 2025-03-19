<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$sql = "SELECT * FROM investimentos WHERE user_id = ?";
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
    <title>Eco Flow | Investimentos</title>
    <link rel="stylesheet" href="../assets/css/tabela.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Investimentos</h2>
            <a href="cadastro/investimento.php" class="btn"><i class="bi bi-plus-circle"></i> Novo
                Invesimento</a>
        </div>
        <div class="table-container">
            <table>
                <tr>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Custo</th>
                    <th>Rendimento</th>
                    <th>Frequencia</th>
                    <th>Vencimento</th>
                    <th colspan="2">Ações</th>
                </tr>
                <tr>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<td>" . $row['tipo'] . "</td>";
                        echo "<td>" . $row['nome'] . "</td>";
                        echo "<td>R$ " . number_format($row['custo'], 2, ',', '.') . "</td>";
                        echo "<td>" . number_format($row['rendimento'], 2, ',') . "%</td>";
                        echo "<td>" . $row['frequencia'] . "</td>";
                        echo "<td>" . (isset($row['vencimento']) ? $row['vencimento'] : "N/A") . "</td>";
                        echo "
                    <td>
                        <form action='editar/investimento.php' method='POST'>
                            <input type='hidden' name='id' value='" . $row['id'] . "'>
                            <button type='submit' class='btn-delete'><i class='bi bi-pencil'></i></button>
                        </form>
                    </td>";
                        echo "
                    <td>
                        <form action='../backend/database/investimentos/deletar.php' method='POST'>
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