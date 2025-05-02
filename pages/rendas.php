<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

// Capturar o mês selecionado na URL ou usar o mês atual como padrão
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : date('n') - 1;
$dbMonth = $selectedMonth + 1; // Ajustar para o formato do banco (1-12)

// Atualizar a consulta para filtrar rendas pelo mês selecionado
$sql = "SELECT * FROM rendas WHERE user_id = ? AND (MONTH(data) = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $_SESSION['id'], $dbMonth);
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
                <div class="data-container">
                    <button id="monthButton"><i class="bi bi-caret-down-fill"></i> </button>
                    <ul id="monthList" style="display: none;">
                        <li data-month="0">Janeiro</li>
                        <li data-month="1">Fevereiro</li>
                        <li data-month="2">Março</li>
                        <li data-month="3">Abril</li>
                        <li data-month="4">Maio</li>
                        <li data-month="5">Junho</li>
                        <li data-month="6">Julho</li>
                        <li data-month="7">Agosto</li>
                        <li data-month="8">Setembro</li>
                        <li data-month="9">Outubro</li>
                        <li data-month="10">Novembro</li>
                        <li data-month="11">Dezembro</li>
                    </ul>
                </div>
                <a href="cadastro/renda.php" class="btn"><i class="bi bi-plus-circle"></i> Nova Renda</a>
            </div>
        </div>
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
    <?php include("../backend/includes/div_erro.php") ?>
    <script>
        const monthNames = [
            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
        ];

        const urlParams = new URLSearchParams(window.location.search);
        const selectedMonth = urlParams.has('month') ? parseInt(urlParams.get('month')) : new Date().getMonth();

        const monthButton = document.getElementById('monthButton');
        const monthList = document.getElementById('monthList');
        const monthItems = monthList.querySelectorAll('li');

        // Exibir o mês selecionado no botão
        monthButton.innerHTML = `<i class="bi bi-caret-down-fill"></i> ${monthNames[selectedMonth]}`;

        // Alternar a exibição da lista de meses ao clicar no botão
        monthButton.addEventListener('click', () => {
            monthList.style.display = monthList.style.display === 'none' ? 'block' : 'none';
        });

        // Fechar a lista ao clicar fora dela
        document.addEventListener('click', (event) => {
            if (!monthButton.contains(event.target) && !monthList.contains(event.target)) {
                monthList.style.display = 'none';
            }
        });

        // Tornar os itens da lista clicáveis e enviar o mês selecionado ao backend
        monthItems.forEach((item) => {
            item.addEventListener('click', () => {
                const selectedMonth = item.getAttribute('data-month');
                monthButton.innerHTML =
                    `<i class="bi bi-caret-down-fill"></i> ${monthNames[selectedMonth]}`;
                monthList.style.display = 'none';

                // Atualizar a página com o mês selecionado
                const url = new URL(window.location.href);
                url.searchParams.set('month', selectedMonth);
                window.location.href = url.toString();
            });
        });
    </script>
</body>

</html>