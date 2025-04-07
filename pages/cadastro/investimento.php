<?php require_once("../../backend/includes/valida.php") ?>
<?php
$dataAtual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Investimentos</title>
    <link rel="stylesheet" href="../../assets/css/form.css?v=<?php echo time(); ?>">
    <?php include("../../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2>Cadastro de Investimentos</h2>
        <button onclick="window.history.back()" id="btn-voltar">
            <i class="bi bi-arrow-left"></i>
        </button>

        <div class="form-container">
            <form action="../../backend/database/investimentos/cadastrar.php" method="POST">
                <div class="top-form">
                    <!-- Informações Gerais -->
                    <div class="card">
                        <h3>Informações Gerais</h3>
                        <div class="form-group">
                            <label for="nome_investimento">Nome do Investimento</label>
                            <input type="text" id="nome_investimento" name="nome_investimento" required>
                        </div>
                    </div>

                    <!-- Tipo de Investimento -->
                    <div class="card">
                        <h3>Tipo de Investimento</h3>
                        <div class="form-group">
                            <label for="tipo_investimento">Tipo</label>
                            <select id="tipo_investimento" name="tipo_investimento" required>
                                <option value="FII">FII</option>
                                <option value="Ação">Ação</option>
                                <option value="Renda Fixa">Renda Fixa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custos, Rendimentos e Data -->
                    <div class="card">
                        <h3>Custos e Rendimentos</h3>
                        <div class="form-group">
                            <label for="custo">Custo</label>
                            <input type="number" id="custo" name="custo" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="recorrente">Recorrente</label>
                            <select id="recorrente" name="recorrente" required>
                                <option value="Sim">Sim</option>
                                <option value="Não">Não</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" id="data" name="data" value="<?php echo $dataAtual->format('Y-m-d'); ?>"
                                required>
                        </div>
                    </div>
                </div>

                <!-- Botão de Envio -->
                <div class="container-btn">
                    <button type="submit">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>