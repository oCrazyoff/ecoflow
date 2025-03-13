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
    }

    .container-busca form {
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        margin-bottom: 20px;
    }

    .container-busca input {
        padding: 10px;
        border: 1px solid #218380;
        border-radius: 5px;
        margin-bottom: 10px;
    }

    .container-busca select {
        padding: 10px;
        border: 1px solid #218380;
        border-radius: 5px;
    }

    .container-busca button {
        padding: 10px;
        background-color: #218380;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 1em;
        transition: .2s ease all;
    }

    .container-busca button:hover {
        background-color: #1c6b63;
    }

    .container-resposta table {
        width: 100%;
        border-collapse: collapse;
    }

    .container-resposta table th {
        background-color: #218380;
        color: white;
        padding: 10px;
    }

    .container-resposta table td {
        padding: 10px;
        border-bottom: 1px solid #218380;
    }

    .container-resposta table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .container-resposta table tr:hover {
        background-color: #f2f2f2;
    }

    .container-resposta table button {
        padding: 5px;
        background-color: #218380;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: .2s ease all;
    }

    .container-resposta table button:hover {
        background-color: #1c6b63;
    }

    .container-resposta table button+button {
        background-color: #ff4d4d;
    }

    .container-resposta table button+button:hover {
        background-color: #e63a3a;
    }

    .container-resposta table th,
    .container-resposta table td {
        text-align: center;
    }

    /* Loading screen styles */
    .loading-screen {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .loading-screen.active {
        display: flex;
    }

    .loading-spinner {
        border: 16px solid #f3f3f3;
        border-top: 16px solid #218380;
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
    </style>
</head>

<body>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2>Cadastro de Investimentos</h2>
        <div class="container-busca">
            <form id="formBusca">
                <input type="text" id="nomeInvestimento" placeholder="Buscar Investimento" required>
                <select name="tipo" id="tipo" required>
                    <option value="" disabled selected>Selecione o tipo</option>
                    <option value="acao">Ação</option>
                    <option value="fii">FII</option>
                    <option value="rendafixa">Renda Fixa</option>
                </select>
                <button type="submit">Buscar</button>
            </form>
        </div>
        <div class="container-resposta">
            <table id="tabelaInvestimentos">
                <tr>
                    <th>Tipo</th>
                    <th>Nome</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Rendimento</th>
                    <th>Frequencia</th>
                </tr>
            </table>
        </div>
    </div>

    <!-- Loading screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-spinner"></div>
    </div>

    <script>
    document.querySelector('#formBusca').addEventListener("submit", async (e) => {
        e.preventDefault();

        const tipo = document.querySelector('#tipo').value;
        const nome = document.querySelector('#nomeInvestimento').value;

        // Show loading screen
        const loadingScreen = document.getElementById('loadingScreen');
        loadingScreen.classList.add('active');

        const resposta = await fetch("buscar_investimento.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                tipo,
                nome
            })
        });

        const data = await resposta.json();

        // Hide loading screen
        loadingScreen.classList.remove('active');

        if (data.erro) {
            alert(data.erro);
            return;
        }

        const tabela = document.getElementById('tabelaInvestimentos');
        let novaLinha = '';

        if (tipo === "acao") {
            novaLinha = `
            <tr>
                <td>Ação</td>
                <td>${data.nome}</td>
                <td>${data.valor}</td>
                <td><input type="date" id="data" name="data"></td>
                <td>${data.rendimento}</td>
                <td>${data.recorrencia}</td>
            </tr>
            `;
        } else if (tipo === "fii") {
            novaLinha = `
            <tr>
                <td>FII</td>
                <td>${data.nome}</td>
                <td>${data.valor}</td>
                <td><input type="date" id="data" name="data"></td>
                <td>${data.rendimento}</td>
                <td>${data.recorrencia}</td>
            </tr>
            `;
        } else if (tipo === "rendafixa") {
            novaLinha = `
            <tr>
                <td>Renda Fixa</td>
                <td>${data.nome}</td>
                <td>${data.valor}</td>
                <td><input type="date" id="data" name="data"></td>
                <td>${data.rendimento}</td>
                <td>${data.vencimento}</td>
            </tr>
            `;
        }

        tabela.innerHTML += novaLinha;

        const hoje = new Date();
        const ano = hoje.getFullYear();
        const mes = String(hoje.getMonth() + 1).padStart(2, '0');
        const dia = String(hoje.getDate()).padStart(2, '0');

        const dataHoje = `${ano}-${mes}-${dia}`;

        document.getElementById('data').value = dataHoje;
    });
    </script>
</body>

</html>