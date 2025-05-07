<?php
session_start();
include("backend/config/database.php");

if (isset($_SESSION['id'])) {
    header("Location: pages/dashboard.php");
    exit;
}

if (!isset($_SESSION['id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['id'] = $_COOKIE['user_id'];

    $sql = "SELECT nome, email FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($nome, $email);
    $stmt->fetch();

    $_SESSION['nome'] = $nome;
    $_SESSION['email'] = $email;

    header('Location: pages/dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow</title>
    <link rel="stylesheet" href="assets/css/index.css?v=<?php echo time(); ?>">
    <?php include("backend/includes/head.php") ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="main-content">
        <header id="header">
            <div class="interface">
                <div class="left">
                    <img src="<?php echo BASE_URL ?>assets/img/logo.png" alt="Logo Eco Flow">
                    <p>EcoFlow</p>
                </div>
                <div class="right">
                    <nav>
                        <a href="#">Início</a>
                        <a href="#beneficios">Beneficios</a>
                        <a href="#vantagens">Vantagens</a>
                        <a href="#contato">Contato</a>
                    </nav>
                    <a id="btn-login" href="<?php echo BASE_URL ?>login.php">Login</a>
                    <a id="btn-cadastro" href="<?php echo BASE_URL ?>cadastro.php">Cadastro</a>
                </div>
            </div>
        </header>
        <section id="hero">
            <div class="interface">
                <div class="left">
                    <h1>Organize suas finanças com inteligência</h1>
                    <p>
                        Simplifique o controle de rendas, despesas e investimentos em um só lugar. Tome decisões
                        financeiras com confiança e alcance seus objetivos.
                    </p>
                    <a id="btn-cadastro" href="<?php echo BASE_URL ?>cadastro.php">Abrir conta</a>
                </div>
                <div class="right">
                    <img src="<?php echo BASE_URL ?>assets/img/index_img.png" alt="Hero">
                </div>
            </div>
        </section>
        <section id="beneficios">
            <div class="interface">
                <div class="txt-container">
                    <h2>Beneficios</h2>
                    <p>
                        Gerencie todos os aspectos da sua vida financeira com nossas ferramentas intuitivas.
                    </p>
                </div>
                <div class="cards-container">
                    <div class="card">
                        <i class="bi bi-wallet"></i>
                        <h3>Rendas</h3>
                        <p>
                            Registre e categorize suas fontes de renda. Visualize a evolução e planeje com base em
                            expectativas futuras.
                        </p>
                    </div>
                    <div class="card">
                        <i class="bi bi-graph-down-arrow"></i>
                        <h3>Despesas</h3>
                        <p>
                            Controle suas despesas pagas e pendentes. Identifique padrões e reduza gastos
                            desnecessários.
                        </p>
                    </div>
                    <div class="card">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h3>Investimentos</h3>
                        <p>Acompanhe o desempenho dos seus investimentos. Análise histórica e projeções para o futuro.
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section id="vantagens">
            <div class="interface">
                <div class="txt-van">
                    <p>Por que escolher EcoFlow?</p>
                    <h3>Tome controle da sua vida financeira</h3>
                    <div class="container-vantagens">
                        <div class="vantagem">
                            <i class="bi bi-check2-circle"></i>
                            <div class="txt-vantagem">
                                <p class="titulo">
                                    Visão clara das suas finanças
                                </p>
                                <p>Veja exatamente para onde vai seu dinheiro e como otimizar seus recursos.</p>
                            </div>
                        </div>
                        <div class="vantagem">
                            <i class="bi bi-check2-circle"></i>
                            <div class="txt-vantagem">
                                <p class="titulo">
                                    Nunca mais perca prazos
                                </p>
                                <p>Receba alertas sobre contas a pagar e evite juros e multas desnecessárias.</p>
                            </div>
                        </div>
                        <div class="vantagem">
                            <i class="bi bi-check2-circle"></i>
                            <div class="txt-vantagem">
                                <p class="titulo">
                                    Gráficos e relatórios detalhados
                                </p>
                                <p>Analise seu comportamento financeiro com visualizações claras e objetivas.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grafico-van">
                    <div class="txt-grafico">
                        <i class="bi bi-pie-chart"></i>
                        <p class="titulo">Distribuição de despesas</p>
                        <p>Visão geral de maio 2024</p>
                    </div>
                </div>
            </div>
        </section>
        <footer>
            <div class="interface">
                <div class="footer-content">
                    <div class="footer-left">
                        <h3>Eco Flow</h3>
                        <p>Seu parceiro na jornada rumo à liberdade financeira.</p>
                    </div>

                    <div class="footer-links">
                        <h4>Links rápidos</h4>
                        <ul>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="cadastro.php">Abrir conta</a></li>
                        </ul>
                    </div>

                    <div class="footer-contact">
                        <h4>Contato</h4>
                        <p>Email: suporte@ecoflow.com</p>
                        <p>Instagram: @eco.flow</p>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> Eco Flow. Todos os direitos reservados.</p>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>