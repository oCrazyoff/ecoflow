<?php require_once("../../backend/includes/valida.php") ?>
<?php
$dataAtual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Rendas</title>
    <link rel="stylesheet" href="../../assets/css/form.css?v=<?php echo time(); ?>">
    <?php include("../../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2>Cadastro de Rendas</h2>
        <button onclick="window.history.back()" id="btn-voltar">
            <i class="bi bi-arrow-left"></i>
        </button>

        <div class="form-container">
            <form action="../../backend/database/rendas/cadastrar.php" method="POST">
                <div class="top-form">
                    <div class="card">
                        <h3>Informações da Renda</h3>
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
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select id="tipo" name="tipo" required>
                                <option value="Ativo">Ativo</option>
                                <option value="Passivo">Passivo</option>
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