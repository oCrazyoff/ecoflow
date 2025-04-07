<?php
session_start();

if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = hash('sha256', random_bytes(32)); // Gera um token CSRF
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Cadastre-se</title>
    <link rel="stylesheet" href="assets/css/form_index.css">
    <?php include("backend/includes/head.php") ?>
</head>

<body>
    <div class="main-content">
        <a class="btn-voltar" href="index.php"><i class="bi bi-caret-left"></i> Voltar</a>
        <div class="form-container">
            <h2>Login</h2>
            <form action="backend/auth/login.php" method="POST">
                <input type="hidden" name="_csrf" value="<?php echo $_SESSION['_csrf']; ?>">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Seu E-mail:" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Sua senha:" required>
                </div>
                <button type="submit">Entrar</button>
            </form>
            <div class="login">
                <p>Não tem conta?</p>
                <a href="cadastro.php">Cadastre-se</a>
            </div>
        </div>
    </div>
    <?php include("backend/includes/div_erro.php") ?>
</body>

</html>