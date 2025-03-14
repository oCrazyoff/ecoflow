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
        <table>
            <tr>
                <th>Tipo</th>
                <th>Nome</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Rendimento</th>
                <th>Frequencia</th>
                <th colspan="2">Ações</th>
            </tr>
            <tr>
                <td>Ação</td>
                <td>KNCR11</td>
                <td>R$ 101,66</td>
                <td>01/01/2021</td>
                <td>9,56%</td>
                <td>Mensal</td>
                <td><button class="btn-edit"><i class="bi bi-pencil"></i></button></td>
                <td><button class="btn-delete"><i class="bi bi-trash"></i></button></td>
        </table>
    </div>
</body>

</html>