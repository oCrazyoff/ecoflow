<?php
if (!isset($_SESSION['id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['id'] = $_COOKIE['user_id'];

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
                    <p>Eco Flow</p>
                </div>
                <div class="right">
                    <a id="btn-login" href="<?php echo BASE_URL ?>login.php">Login</a>
                    <a id="btn-cadastro" href="<?php echo BASE_URL ?>cadastro.php">Cadastro</a>
                </div>
            </div>
        </header>
        <section id="hero">
            <div class="interface">
                <div class="left">
                    <h1>Vem ser <br><span>Eco Flow</span></h1>
                    <p>
                        Gerencie seus gastos, acompanhe suas rendas e automatize seus investimentos em um só lugar. O
                        EcoFlow simplifica seu planejamento financeiro para que você tenha mais controle e liberdade.
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
                    <h2>Por que usar o Eco Flow?</h2>
                    <p>
                        O Eco Flow foi criado para facilitar a forma como você lida com dinheiro. Esqueça as planilhas
                        complicadas — aqui, tudo é intuitivo, automatizado e feito pra te dar mais controle com menos
                        esforço.
                    </p>
                </div>
                <div class="cards-container">
                    <div class="card">
                        <div class="img-container">
                            <img src="assets/img/ben_img1.png" alt="Imagem Controle de Gastos">
                        </div>
                        <h3>Controle de Gastos</h3>
                        <p>Veja para onde seu dinheiro está indo com gráficos simples e relatórios inteligentes.</p>
                    </div>
                    <div class="card">
                        <div class="img-container">
                            <img src="assets/img/ben_img2.png" alt="Imagem Renda Automatizadas">
                        </div>
                        <h3>Renda Automatizada</h3>
                        <p>Automatize sua renda passiva e veja ela crescer com o tempo, mês a mês.</p>
                    </div>
                    <div class="card">
                        <div class="img-container">
                            <img src="assets/img/ben_img3.png" alt="Imagem Investimentos Inteligentes">
                        </div>
                        <h3>Investimentos Inteligentes</h3>
                        <p>Planeje seus investimentos com metas claras e acompanhamento automático.</p>
                    </div>
                    <div class="card">
                        <div class="img-container">
                            <img src="assets/img/ben_img4.png" alt="Imagem Segurança e Privacidade">
                        </div>
                        <h3>Segurança e Privacidade</h3>
                        <p>Seus dados protegidos com criptografia de ponta e autenticação em dois fatores.</p>
                    </div>
                </div>
            </div>
        </section>
        <section id="como-funciona">
            <div class="interface">
                <div class="txt-container">
                    <h2>Como começar?</h2>
                    <p>
                        Começar com o Eco Flow é simples. Pensamos em cada etapa para que você foque no que importa:
                        entender seu dinheiro, planejar seus próximos passos e ver seus resultados com clareza.
                    </p>
                </div>
                <div class="steps">
                    <div class="step">
                        <span>
                            <i class="bi bi-bank"></i>
                        </span>
                        <div class="txt">
                            <h3>Crie sua conta</h3>
                            <p>Leva menos de 1 minuto e é totalmente gratuito.</p>
                        </div>
                    </div>
                    <div class="step">
                        <span>
                            <i class="bi bi-card-checklist"></i>
                        </span>
                        <div class="txt">
                            <h3>Organize sua vida financeira</h3>
                            <p>Cadastre seus gastos, rendas e investimentos.</p>
                        </div>
                    </div>
                    <div class="step">
                        <span>
                            <i class="bi bi-airplane"></i>
                        </span>
                        <div class="txt">
                            <h3>Alcance a liberdade</h3>
                            <p>Acompanhe seus resultados e planeje o futuro com mais clareza.</p>
                        </div>
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