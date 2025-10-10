<?php
$n_valida = true;
require_once "includes/inicio.php"
?>
<header class="sticky top-0 left-0 bg-white py-2 h-[4rem] border-b border-borda shadow-lg">
    <div class="interface h-full flex items-center justify-between">
        <h1 class="logo text-verde">Eco<span>Flow</span></h1>
        <nav class="flex gap-3 items-center justify-center">
            <a class="btn-header btn-entrar" href="login">Entrar</a>
            <a class="btn-header btn-cadastrar" href="cadastro">Cadastre-se</a>
        </nav>
    </div>
</header>

<main>
    <section class="bg-gradient-to-tr from-verde-hover to-verde h-auto lg:h-[calc(100dvh-4rem)] py-10">
        <div class="interface h-full flex gap-5 items-center justify-between">
            <div class="txt-hero">
                <p class="font-semibold px-5 py-1 rounded-full bg-white/20 w-max text-sm">
                    Finanças pessoais simplificadas
                </p>
                <h2 class="text-3xl lg:text-5xl font-bold text-center lg:text-left">Organize as suas finanças com
                    inteligência</h2>
                <p class="text-lg lg:text-xl text-white/70 text-center lg:text-left">
                    Simplifique o controle de rendas e despesas em um só lugar. Tome decisões financeiras
                    com confiança e alcance os seus objetivos.
                </p>
                <div class="flex items-center gap-5 w-full lg:w-auto">
                    <a class="btn-hero" href="cadastro">Começar agora</a>
                    <a class="btn-hero" href="#sobre">Saiba mais</a>
                </div>
            </div>
            <img class="img-hero hidden lg:block" src="assets/img/img-hero.svg" alt="Desenho de finanças">
        </div>
        <a href="#sobre" class="absolute bottom-10 left-1/2 animate-bounce text-5xl text-white/50 hidden lg:block">
            <i class="bi bi-arrow-down-short"></i>
        </a>
    </section>

    <section id="sobre">
        <div class="interface flex flex-col gap-5 items-center justify-center py-10">
            <p class="font-semibold px-5 py-1 rounded-full bg-verde/20 text-verde w-max text-sm">Módulos Principais</p>
            <h2 class="text-3xl font-bold text-center">Gerencie toda sua vida financeira</h2>
            <p class="text-xl text-center text-texto-opaco w-full lg:w-1/2">
                Nossas ferramentas intuitivas ajudam você a ter controle total sobre suas finanças.
            </p>
            <div class="container-cards-sobre">
                <div class="card-sobre">
                    <i class="bi bi-wallet"></i>
                    <h3>Rendas</h3>
                    <p>
                        Registre e categorize suas fontes de renda. Visualize a evolução e planeje com base em
                        expectativas futuras.
                    </p>
                </div>
                <div class="card-sobre">
                    <i class="bi bi-graph-down-arrow"></i>
                    <h3>Despesas</h3>
                    <p>
                        Controle suas despesas pagas e pendentes. Identifique padrões e reduza gastos desnecessários.
                    </p>
                </div>
                <div class="card-sobre">
                    <i class="bi bi-columns-gap"></i>
                    <h3>Dashboard</h3>
                    <p>
                        Visualize os gráficos e um resumo do mês para ter controle dos gastos.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="vantagens">
        <div class="interface flex flex-col-reverse lg:flex-row gap-5 items-center justify-between">
            <div class="txt-vantagens">
                <p class="font-semibold px-5 py-1 rounded-full bg-verde/20 text-verde w-max text-sm">Por que escolher
                    EcoFlow?</p>
                <h2 class="text-3xl lg:text-4xl font-bold">Tome controle da sua vida financeira</h2>
                <div class="lista-vantagens">
                    <div class="item-lista">
                        <i class="bi bi-check2-circle"></i>
                        <div class="txt-vantagem">
                            <h3>Visão clara das suas finanças</h3>
                            <p>Veja exatamente para onde vai seu dinheiro e como otimizar seus recursos.</p>
                        </div>
                    </div>
                    <div class="item-lista">
                        <i class="bi bi-check2-circle"></i>
                        <div class="txt-vantagem">
                            <h3>Disponível sempre que precisar</h3>
                            <p>Use tanto no site no computador ou celular, tanto também no aplicativo mobile.</p>
                        </div>
                    </div>
                    <div class="item-lista">
                        <i class="bi bi-check2-circle"></i>
                        <div class="txt-vantagem">
                            <h3>Gráficos e relatórios detalhados</h3>
                            <p>Analise seu comportamento financeiro com visualizações claras e objetivas.</p>
                        </div>
                    </div>
                </div>
                <a href="cadastro">Criar conta grátis</a>
            </div>
            <img class="w-full lg:w-1/3 mb-10 lg:mb-0" src="assets/img/img-vantagens.svg"
                 alt="Desenho administrando economias">
        </div>
    </section>

    <section class="bg-gradient-to-tr from-verde-hover to-verde py-10">
        <div class="interface flex flex-col gap-5 items-center justify-center text-white">
            <h2 class="text-3xl lg:text-4xl font-bold text-center">Pronto para organizar suas finanças?</h2>
            <p class="text-xl text-white/70 text-center w-full lg:w-1/2">
                Comece agora mesmo a transformar a sua relação com o dinheiro e alcançar seus objetivos financeiros.
            </p>
            <div class="flex flex-col lg:flex-row w-full justify-center items-center gap-5">
                <a class="btn-chamada" href="cadastro">Criar conta grátis</a>
                <a class="btn-chamada" href="login">Fazer login</a>
            </div>
        </div>
    </section>

    <footer class="flex items-center justify-center bg-black text-white p-5 text-center">
        <p>&copy; <?= date('Y') ?> EcoFlow. Todos os direitos reservados.</p>
    </footer>
</main>
<?php require_once "includes/fim.php" ?>
