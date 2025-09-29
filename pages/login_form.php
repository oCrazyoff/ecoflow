<?php
$n_valida = true;
require_once "includes/inicio.php"
?>
<main>
    <div class="container-form-index">
        <form action="fazer_login" method="POST">
            <!--csrf-->
            <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
            <h1>Eco<span>Flow</span></h1>
            <h2>Login</h2>
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" placeholder="Email">
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="Senha">
            </div>
            <button><i class="bi bi-arrow-bar-right"></i> Entrar</button>
            <p>Ñão tem uma conta? <a href="#">Cadastre-se</a></p>
        </form>
    </div>
</main>
<?php require_once "includes/fim.php" ?>
