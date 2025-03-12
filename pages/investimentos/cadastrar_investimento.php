<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Investimentos</title>
    <?php include("../../backend/includes/head.php") ?>
    <style>
        form {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1em;
            flex-wrap: wrap;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 65%;
        }

        h2 {
            color: #218380;
            text-align: center;
            margin-bottom: .5em;
        }

        .card {
            background: #ffffff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.1em;
            font-weight: bold;
            color: #218380;
            margin-bottom: 10px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }

        select,
        input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #218380;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background: #176d5c;
        }
    </style>
</head>

<body>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2>Cadastro de Investimentos</h2>
        <form id="form-investimento" onsubmit="enviarFormulario(event)">
            <div class="card">
                <div class="section-title">Informações Gerais</div>
                <label for="nome">Nome/Descrição:</label>
                <input type="text" id="nome" name="nome" placeholder="Nome do investimento" required>

                <label for="tipo">Tipo de Investimento:</label>
                <select id="tipo" name="tipo" onchange="atualizarCampos()" required>
                    <option value="acao">Ações</option>
                    <option value="fii">FII</option>
                    <option value="renda_fixa">Renda Fixa</option>
                </select>
            </div>

            <div class="card">
                <div class="section-title">Detalhes Financeiros</div>
                <label for="rendimento">Rendimento (% ao ano):</label>
                <input type="number" step="0.01" id="rendimento" name="rendimento" placeholder="Ex: 10" required>

                <label for="valor">Valor Investido:</label>
                <input type="number" step="0.01" id="valor" name="valor" placeholder="Ex: 1000" required>
            </div>

            <div class="card">
                <div class="section-title">Configuração da Compra</div>
                <label for="data_compra">Data de Compra:</label>
                <input type="date" id="data_compra" name="data_compra" value="" required>

                <label for="frequencia">Frequência:</label>
                <select id="frequencia" name="frequencia" required>
                    <option value="diaria">Diária</option>
                    <option value="mensal">Mensal</option>
                    <option value="anual">Anual</option>
                </select>
            </div>

            <div class="card" id="campos-adicionais">
                <div class="section-title">Detalhes Específicos</div>
            </div>

            <button type="submit">Cadastrar</button>
        </form>
    </div>
    <script>
        document.getElementById("data_compra").valueAsDate = new Date();

        function atualizarCampos() {
            const tipo = document.getElementById("tipo").value;
            const campos = document.getElementById("campos-adicionais");
            campos.innerHTML = '<div class="section-title">Detalhes Específicos</div>';

            if (tipo === "acao" || tipo === "fii") {
                campos.innerHTML += `
                    <label for="ticker">Ticker:</label>
                    <input type="text" id="ticker" name="ticker" placeholder="Ex: PETR4" required>
                    <label for="quantidade">Quantidade:</label>
                    <input type="number" id="quantidade" name="quantidade" placeholder="Número de cotas ou ações" required>
                `;
            } else if (tipo === "renda_fixa") {
                campos.innerHTML += `
                    <label for="emissor">Emissor:</label>
                    <input type="text" id="emissor" name="emissor" placeholder="Ex: Tesouro Nacional" required>
                    <label for="vencimento">Data de Vencimento:</label>
                    <input type="date" id="vencimento" name="vencimento" required>
                `;
            }
        }

        function enviarFormulario(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById("form-investimento"));
            const dados = {};
            formData.forEach((value, key) => dados[key] = value);
            console.log("Dados enviados:", dados);
            alert("Investimento cadastrado com sucesso!");
        }
    </script>

</body>

</html>