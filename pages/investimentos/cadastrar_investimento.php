<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Investimentos</title>
    <?php include("../../backend/includes/head.php") ?>
    <script>
        function atualizarFormulario() {
            var tipo = document.getElementById("tipo").value;
            var extraFields = document.getElementById("extraFields");

            var campos = {
                "acao": "<label>Ticker:</label><input type='text' id='ticker' name='ticker' required onkeyup='buscarInvestimentos(\"acao\")'><br>",
                "fii": "<label>Código do FII:</label><input type='text' id='codigo_fii' name='codigo_fii' required onkeyup='buscarInvestimentos(\"fii\")'><br>",
                "renda_fixa": "<label>Tipo de Renda Fixa:</label><input type='text' name='tipo_renda_fixa' required><br>"
            };

            extraFields.innerHTML = campos[tipo] || "";
        }

        function atualizarFormulario() {
            var tipo = document.getElementById("tipo").value;
            var extraFields = document.getElementById("extraFields");

            var campos = {
                "acao": "<label>Ticker:</label><input type='text' id='ticker' name='ticker' required onkeyup='buscarInvestimentos(\"acao\")'><br>",
                "fii": "<label>Código do FII:</label><input type='text' id='codigo_fii' name='codigo_fii' required onkeyup='buscarInvestimentos(\"fii\")'><br>",
                "renda_fixa": "<label>Tipo de Renda Fixa:</label><input type='text' name='tipo_renda_fixa' required onkeyup='buscarInvestimentos(\"renda_fixa\")'><br>"
            };

            extraFields.innerHTML = campos[tipo] || "";
        }
    </script>
</head>

<body>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2>Cadastrar Investimento</h2>
        <form action="salvar_investimento.php" method="post">
            <label>Tipo de Investimento:</label>
            <select name="tipo" id="tipo" onchange="atualizarFormulario()" required>
                <option value="">Selecione</option>
                <option value="acao">Ação</option>
                <option value="fii">Fundo Imobiliário</option>
                <option value="renda_fixa">Renda Fixa</option>
            </select>
            <br>

            <label>Nome do Investimento:</label>
            <input type="text" name="nome" required>
            <br>

            <label>Valor Investido:</label>
            <input type="number" name="valor" step="0.01" required>
            <br>

            <label>Data de Compra:</label>
            <input type="date" name="data_compra" required>
            <br>

            <div id="extraFields"></div> <!-- Campos dinâmicos aqui -->

            <button type="submit">Cadastrar</button>
        </form>
    </div>
</body>

</html>