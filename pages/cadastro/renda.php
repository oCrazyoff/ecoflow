<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Rendas</title>
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
            width: 100%;
        }

        .form-container .top-form {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1em;
        }

        .card {
            width: 100%;
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
        <h2>Cadastro de Rendas</h2>
        <button onclick="window.history.back()" id="btn-voltar">
            <i class="bi bi-arrow-left"></i>
        </button>

        <div class="form-container">
            <form action="processar_renda.php" method="POST">
                <div class="top-form">
                    <div class="card">
                        <h3>Informações da Renda</h3>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" id="descricao" name="descricao" required>
                        </div>
                        <div class="form-group">
                            <label for="valor">Valor</label>
                            <input type="number" id="valor" name="valor" required>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Configuração da Renda</h3>
                        <div class="form-group">
                            <label for="frequencia">Frequência</label>
                            <select id="frequencia" name="frequencia" required>
                                <option value="Mensal">Mensal</option>
                                <option value="Diária">Diária</option>
                                <option value="Anual">Anual</option>
                                <option value="Trimestral">Trimestral</option>
                                <option value="Bimestral">Bimestral</option>
                            </select>
                        </div>
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