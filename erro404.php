<?php
$n_valida = true;
$titulo = "Erro 404";
require_once "includes/inicio.php";
?>
    <main class="flex flex-col gap-5 items-center justify-center min-h-dvh">
        <h1 class="text-9xl text-verde font-bold">404</h1>
        <h2 class="text-center text-3xl font-bold">Página não encontrada</h2>
        <p class="text-center text-branco-texto-opaco text-2xl">A página que você está procurando não existe.</p>
        <a class="rounded-lg p-3 px-10 text-white text-xl bg-verde hover:bg-verde-hover"
           href="<?= BASE_URL . "dashboard" ?>">
            Voltar ao Início
        </a>
    </main>
<?php require_once "includes/fim.php"; ?>