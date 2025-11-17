<?php
$n_valida = true;
require_once "includes/layout/inicio.php"
?>
<main class="pb-0">
    <div class="container-form-index">
        <form action="fazer_cadastro" method="POST">
            <!--csrf-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
            <h1>Eco<span>Flow</span></h1>
            <h2>Cadastre-se</h2>
            <div class="input-group">
                <label for="nome">Nome</label>
                <input type="text" name="nome" id="nome" placeholder="Digite seu nome">
            </div>
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" placeholder="Digite seu e-mail">
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Digite sua senha">
            </div>
            <div class="input-group">
                <label for="confirma-senha">Confirmar Senha</label>
                <input type="password" name="confirma-senha" id="confirma-senha"
                    placeholder="Digite sua senha novamente">
            </div>
            <button>Criar Conta</button>
            <p>Já tem uma conta? <a href="login">Login</a></p>
        </form>
    </div>
</main>
<?php require_once "includes/layout/fim.php" ?>