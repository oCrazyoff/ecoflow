<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Configurações</title>
    <link rel="stylesheet" href="../frontend/css/config.css">
    <?php include("../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <div class="titulo">
            <h2>Configurações</h2>
        </div>
        <div class="config-section">
            <h3>Perfil</h3>
            <label for="username">Nome de Usuário:</label>
            <input type="text" id="username" name="username">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="config-section">
            <h3>Preferências</h3>
            <label for="language">Idioma:</label>
            <select id="language" name="language">
                <option value="pt">Português</option>
                <option value="en">Inglês</option>
            </select>
            <label for="theme">Tema:</label>
            <select id="theme" name="theme">
                <option value="light">Claro</option>
                <option value="dark">Escuro</option>
            </select>
        </div>
        <div class="config-section">
            <h3>Notificações</h3>
            <label for="email-notifications">Notificações por Email:</label>
            <input type="checkbox" id="email-notifications" name="email-notifications">
            <label for="sms-notifications">Notificações por SMS:</label>
            <input type="checkbox" id="sms-notifications" name="sms-notifications">
        </div>
        <button class="btn-save">Salvar Configurações</button>
    </div>
</body>

</html>