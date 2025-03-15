<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Configurações</title>
    <link rel="stylesheet" href="../assets/css/user_config.css?v=<?php echo time(); ?>">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <h1><i class="bi bi-person-gear"></i> Configurações do Usuário</h1>
        <form>
            <div class="form-group">
                <label for="username"><i class="bi bi-person"></i> Nome de Usuário:</label>
                <input type="text" id="username" name="username">
            </div>
            <div class="form-group">
                <label for="email"><i class="bi bi-envelope"></i> Email:</label>
                <input type="email" id="email" name="email">
            </div>
            <div class="form-group">
                <label for="password"><i class="bi bi-lock"></i> Senha:</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group">
                <label for="confirm-password"><i class="bi bi-lock-fill"></i> Confirmar Senha:</label>
                <input type="password" id="confirm-password" name="confirm-password">
            </div>
            <div class="botoes">
                <button type="submit"><i class="bi bi-save"></i> Salvar</button>
                <a href="../backend/auth/logout.php"><i class="bi bi-box-arrow-left"></i> Trocar conta</a>
            </div>
        </form>
    </div>
</body>

</html>