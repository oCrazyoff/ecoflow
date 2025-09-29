<?php
$n_valida = true;
require_once "includes/inicio.php"
?>
<header class="sticky top-0 left-0 bg-verde py-2 h-[4rem]">
    <div class="interface h-full flex items-center justify-between">
        <h1 class="logo">Eco<span class="text-white">Flow</span></h1>
        <nav class="flex gap-3 items-center justify-center">
            <a class="btn-header" href="login">Login</a>
            <a class="btn-header" href="#">Cadastre-se</a>
        </nav>
    </div>
</header>

<main>
    <section class="h-[calc(100dvh-4rem)]">
        <div class="interface h-full flex flex-col items-center justify-center">
            <h1 class="text-6xl font-bold text-center">
                Tome o controle da sua vida financeira.
            </h1>
            <p class="text-center w-3/4 text-2xl text-texto-opaco py-3">
                Com a EcoFlow, visualizar suas finanças nunca foi tão simples e intuitivo. Rendas, despesas e metas,
                tudo em um só lugar.
            </p>
            <a href="#" class="text-white py-3 px-10 text-2xl rounded-lg bg-verde hover:bg-verde-hover">
                Comece Agora - É Grátis
            </a>
        </div>
    </section>

    <footer class="flex items-center justify-center bg-black text-white p-5">
        <p>&copy; 2025 EcoFlow. Todos os direitos reservados.</p>
    </footer>
</main>
<?php require_once "includes/fim.php" ?>
