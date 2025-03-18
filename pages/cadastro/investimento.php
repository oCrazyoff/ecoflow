<?php require_once("../../backend/includes/valida.php") ?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Investimentos</title>
    <?php include("../../backend/includes/head.php") ?>
    <style>
    h2 {
        color: #218380;
        margin-bottom: 1em;
        font-size: 3em;
    }

    #btn-voltar {
        padding: .2em .5em;
        background-color: #218380;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: .2s ease all;
        position: absolute;
        top: 1em;
        left: 1em;
        font-size: 1.5em;
    }

    #btn-voltar:hover {
        background-color: #1c6b63;
    }

    .form-container {
        margin: 0 auto;
    }

    .form-container .top-form {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1em;
    }

    .card {
        width: 40%;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .card h3 {
        color: #218380;
        margin-bottom: 1em;
    }

    .form-group {
        margin-bottom: 1.5em;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5em;
        color: #333;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 0.8em;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1em;
    }

    .form-group input[type="number"] {
        display: inline-block;
    }

    .form-group select {
        display: inline-block;
    }

    .form-group .frequency-select {
        width: 100%;
    }

    .container-btn {
        display: flex;
        justify-content: right;
        margin-top: 1em;
    }

    button[type="submit"] {
        padding: .5em 1em;
        background-color: #218380;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1.2em;
        transition: 0.3s ease all;
    }

    button[type="submit"]:hover {
        background-color: #1c6b63;
    }
    </style>
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
</body>

</html>