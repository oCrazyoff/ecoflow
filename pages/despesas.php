<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Despesas</title>
    <link rel="stylesheet" href="../frontend/css/tabela.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Despesas</h2>
            <a class="btn"><i class="bi bi-plus-circle"></i> Nova Despesa</a>
        </div>
        <table>
            <tr>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Tipo</th>
                <th colspan="2">Ações</th>
            </tr>
            <tr>
                <td>Aluguel</td>
                <td>R$ 1.000,00</td>
                <td>01/01/2021</td>
                <td>Obrigatoria</td>
                <td><button class="btn-edit"><i class="bi bi-pencil"></i></button></td>
                <td><button class="btn-delete"><i class="bi bi-trash"></i></button></td>
        </table>
    </div>
</body>

</html>