<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Investimentos</title>
    <link rel="stylesheet" href="../frontend/css/tabela.css">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Investimentos</h2>
            <button class="btn"><i class="bi bi-plus-circle"></i> Novo Invesimento</button>
        </div>
        <table>
            <tr>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Rendimento</th>
                <th colspan="2">Ações</th>
            </tr>
            <tr>
                <td>Ações</td>
                <td>R$ 1.000,00</td>
                <td>01/01/2021</td>
                <td>15%</td>
                <td><button class="btn-edit"><i class="bi bi-pencil"></i></button></td>
                <td><button class="btn-delete"><i class="bi bi-trash"></i></button></td>
        </table>
    </div>
</body>

</html>