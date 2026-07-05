<?php
$n_valida = true;
require_once "includes/layout/inicio.php"
    ?>
<main class="min-h-screen flex flex-col lg:flex-row w-full">
    <!-- Imagem e texto (Esquerda) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gray-900">
        <img src="<?= BASE_URL ?>assets/img/login-cadastro.png" alt="Background Financeiro"
            class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="relative z-10 flex flex-col items-center justify-center w-full p-12 text-center h-full">
            <h1 class="text-white text-5xl font-bold leading-tight max-w-xl">
                Gestão financeira simples e inteligente
            </h1>
            <p class="text-white text-xl mt-4 max-w-lg opacity-90">
                Acompanhe suas despesas e alcance suas metas de economia com facilidade.
            </p>
        </div>
    </div>

    <!-- Formulário de Login (Direita) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8 min-h-screen lg:min-h-full">
        <div class="w-full max-w-md flex flex-col items-center">
            <h1 class="text-4xl font-bold text-verde mb-10 text-center">EcoFlow</h1>

            <h2 class="text-3xl font-bold text-gray-800 mb-8 text-center">Bem-vindo de volta</h2>

            <form action="fazer_login" method="POST" class="w-full flex flex-col gap-5">
                <!--csrf-->
                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">

                <!-- Email -->
                <div class="relative flex flex-col w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-envelope text-gray-400 text-lg"></i>
                    </div>
                    <input type="email" name="email" id="email" placeholder="E-mail"
                        class="block w-full pl-10 pr-3 py-3 border border-borda rounded-lg focus:outline-none focus:border-verde focus:ring-1 focus:ring-verde bg-white text-gray-700 placeholder-texto-opaco"
                        required>
                </div>

                <!-- Senha -->
                <div class="relative flex flex-col w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-lock text-gray-400 text-lg"></i>
                    </div>
                    <input type="password" name="senha" id="senha" placeholder="Senha"
                        class="block w-full pl-10 pr-3 py-3 border border-borda rounded-lg focus:outline-none focus:border-verde focus:ring-1 focus:ring-verde bg-white text-gray-700 placeholder-texto-opaco"
                        required>
                </div>

                <button type="submit"
                    class="w-full py-3 px-4 rounded-full cursor-pointer text-lg font-medium text-white bg-verde hover:bg-verde-hover transition-colors mt-2">
                    Entrar
                </button>

                <div class="mt-4 text-center w-full">
                    <p class="text-sm text-gray-600">Não tem uma conta? <a href="cadastro"
                            class="text-verde font-semibold hover:underline">Cadastre-se</a></p>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require_once "includes/layout/fim.php" ?>