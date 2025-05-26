<?php require_once("../../backend/includes/valida.php") ?>
<?php
$dataAtual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Despesas</title>
    <link rel="stylesheet" href="../../assets/css/form.css?v=<?php echo time(); ?>">
    <?php include("../../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../../backend/includes/loading.php") ?>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2>Cadastro de Despesas</h2>
        <button onclick="window.history.back()" id="btn-voltar">
            <i class="bi bi-arrow-left"></i>
        </button>

        <div class="form-container">
            <form action="../../backend/database/despesas/cadastrar.php" method="POST">
                <div class="top-form">
                    <div class="card">
                        <h3>Informações da Despesa</h3>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" id="descricao" name="descricao" required>
                        </div>
                        <div class="form-group">
                            <label for="valor">Valor</label>
                            <input type="number" id="valor" name="valor" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" id="data" name="data" value="<?php echo $dataAtual->format('Y-m-d'); ?>"
                                required>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Configuração da Despesa</h3>
                        <div class="form-group">
                            <label for="frequencia">Status</label>
                            <select id="status" name="status" required>
                                <option value="0">Pendente</option>
                                <option value="1">Pago</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recorrente">Recorrente</label>
                            <select id="recorrente" name="recorrente" required>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="container-btn">
                    <button type="submit">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>