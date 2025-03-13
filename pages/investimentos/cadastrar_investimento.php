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

        .container-tipos {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1em;
        }

        .container-tipos button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 20em;
            height: 20em;
            border: 2px solid #218380;
            color: #1c6b63;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: .2s ease all;
        }

        .container-tipos button i {
            font-size: 4em;
            margin-bottom: 5px;
        }

        .container-tipos button p {
            font-size: 2.5em;
            text-align: center;
        }

        .container-tipos button:hover {
            background-color: #1c6b63;
            color: #fff;
        }

        .buscar-tipos {
            display: none;
            flex-direction: column;
            gap: 1em;
        }

        .buscar-tipos form {
            display: flex;
            flex-direction: column;
            gap: 1em;
        }

        .buscar-tipos form label {
            font-size: 1.2em;
            color: #218380;
        }

        .buscar-tipos form input {
            padding: 10px;
            border: 2px solid #218380;
            border-radius: 5px;
        }

        .buscar-tipos form button {
            padding: 10px;
            background-color: #218380;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: .2s ease all;
        }

        .buscar-tipos form button:hover {
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

        .container-resposta table th,
        .container-resposta table td {
            text-align: center;
        }

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
        <button onclick="location.reload()" id="btn-voltar"><i class="bi bi-arrow-left"></i></button>
        <div class="container-tipos">
            <button id="rendafixa">
                <i class="bi bi-cash-coin"></i>
                <p>Renda Fixa</p>
            </button>
            <button id="fii">
                <i class="bi bi-bank"></i>
                <p>Fundos Imobiliários</p>
            </button>
            <button id="acoes">
                <i class="bi bi-piggy-bank"></i>
                <p>Ações</p>
            </button>
        </div>
        <div class="buscar-tipos">

            <form id="formBusca">
                <label for="nome"><span id="tipo-txt"></span></label>
                <input type="text" id="nome" name="nome" placeholder="Nome do investimento" required>
                <button id="buscar">Buscar</button>
            </form>

            <div class="container-resposta">
                <table id="tabelaInvestimentos"></table>
            </div>

        </div>

        <!-- Loading screen -->
        <div class="loading-screen" id="loadingScreen">
            <div class="loading-spinner"></div>
        </div>

        <script>
            let tipo = "";

            document.getElementById('rendafixa').addEventListener('click', () => {
                tipo = 'rendafixa';
                document.querySelector('.container-tipos').style.display = 'none';
                document.querySelector('.buscar-tipos').style.display = 'flex';
                document.getElementById('tipo-txt').innerText = "Renda Fixa";
            });

            document.getElementById('fii').addEventListener('click', () => {
                tipo = 'fii';
                document.querySelector('.container-tipos').style.display = 'none';
                document.querySelector('.buscar-tipos').style.display = 'flex';
                document.getElementById('tipo-txt').innerText = "Fundo Imobiliário";
            });

            document.getElementById('acoes').addEventListener('click', () => {
                tipo = 'acao';
                document.querySelector('.container-tipos').style.display = 'none';
                document.querySelector('.buscar-tipos').style.display = 'flex';
                document.getElementById('tipo-txt').innerText = "Ação";
            });

            document.querySelector('#formBusca').addEventListener("submit", async (e) => {
                e.preventDefault();

                // Verifica se o tipo foi selecionado
                if (!tipo) {
                    alert("Por favor, selecione um tipo de investimento antes de buscar.");
                    return;
                }

                // Obtém o valor do campo de nome
                const nome = document.getElementById('nome').value;

                // Exibe a tela de carregamento
                const loadingScreen = document.getElementById('loadingScreen');
                loadingScreen.classList.add('active');

                try {
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

                    // Esconde a tela de carregamento
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
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th>Ticker</th>
                        <th>Empresa</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Rendimento</th>
                        <th>Frequência</th>
                    </tr>
                    <tr>
                        <td>Ação</td>
                        <td>${data.nome}</td>
                        <td>${data.ticker}</td>
                        <td>${data.empresa}</td>
                        <td><input type="date" id="data" name="data"></td>
                        <td>${data.valor}</td>
                        <td>${data.rendimento}</td>
                        <td>${data.recorrencia}</td>
                    </tr>
                `;
                    } else if (tipo === "fii") {
                        novaLinha = `
                    <tr>
                        <th>Tipo</th>
                        <th>Ticker</th>
                        <th>Emissor</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Rendimento</th>
                        <th>Frequência</th>
                    </tr>
                    <tr>
                        <td>Fundo Imobiliário</td>
                        <td>${data.ticker}</td>
                        <td>${data.emissor}</td>
                        <td><input type="date" id="data" name="data"></td>
                        <td>${data.valor}</td>
                        <td>${data.rendimento}</td>
                        <td>${data.recorrencia}</td>
                    </tr>
                `;
                    } else if (tipo === "rendafixa") {
                        novaLinha = `
                    <tr>
                        <th>Tipo</th>
                        <th>Nome</th>
                        <th>Emissor</th>
                        <th>Data</th>
                        <th>Valor</th>
                        <th>Rendimento</th>
                        <th>Vencimento</th>
                    </tr>
                    <tr>
                        <td>Renda Fixa</td>
                        <td>${data.nome}</td>
                        <td>${data.emissor}</td>
                        <td><input type="date" id="data" name="data"></td>
                        <td>${data.valor}</td>
                        <td>${data.rendimento}</td>
                        <td>${data.vencimento}</td>
                    </tr>
                `;
                    }

                    tabela.innerHTML = novaLinha;

                    // Define a data de hoje no input
                    const hoje = new Date();
                    const ano = hoje.getFullYear();
                    const mes = String(hoje.getMonth() + 1).padStart(2, '0');
                    const dia = String(hoje.getDate()).padStart(2, '0');
                    const dataHoje = `${ano}-${mes}-${dia}`;

                    document.getElementById('data').value = dataHoje;
                } catch (error) {
                    loadingScreen.classList.remove('active');
                    alert("Erro ao buscar os investimentos. Verifique sua conexão.");
                }
            });
        </script>

    </div>
</body>

</html>