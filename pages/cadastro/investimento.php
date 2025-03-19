<?php require_once("../../backend/includes/valida.php") ?>

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
                        <div class="form-group">
                            <label for="ticker">Ticker</label>
                            <input type="text" id="ticker" name="ticker" required>
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

                    <!-- Custos e Rendimentos -->
                    <div class="card">
                        <h3>Custos e Rendimentos</h3>
                        <div class="form-group">
                            <label for="custo">Custo</label>
                            <input type="number" id="custo" name="custo" required>
                        </div>
                        <div class="form-group">
                            <label for="rendimento">Rendimento</label>
                            <input type="number" id="rendimento" name="rendimento" required>
                        </div>
                        <div class="form-group">
                            <label for="vencimento">Data de Vencimento</label>
                            <input type="date" id="vencimento" name="vencimento">
                        </div>
                    </div>

                    <!-- Frequência de Rendimento -->
                    <div class="card">
                        <h3>Frequência de Rendimento</h3>
                        <div class="form-group">
                            <label for="frequencia">Frequência</label>
                            <select id="frequencia" name="frequencia" required>
                                <option value="Diário">Diário</option>
                                <option value="Mensal">Mensal</option>
                                <option value="Anual">Anual</option>
                                <option value="Bimestral">Bimestral</option>
                                <option value="Trimestral">Trimestral</option>
                            </select>
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
    <script>
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('vencimento').value = hoje;
    </script>
</body>

</html>