<?php
session_start();

if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = hash('sha256', random_bytes(32)); // Gera um token CSRF seguro
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Flow | Cadastre-se</title>
    <?php include("backend/includes/head.php") ?>
    <style>
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            padding: 2em;
            border: 2px solid #218380;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .form-container h2 {
            font-size: 2em;
            color: #218380;
            text-align: center;
            padding-bottom: .2em;
            margin-bottom: 1em;
            border-bottom: 2px solid #218380;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            gap: 1.5em;
        }

        .form-container form .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5em;
        }

        .form-container form .form-group label {
            font-weight: bold;
            color: #333;
        }

        .form-container form .form-group input {
            padding: 0.8em;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-container form .form-group input:focus {
            border-color: #218380;
            outline: none;
            box-shadow: 0 0 5px rgba(33, 131, 128, 0.5);
        }

        button[type="submit"] {
            padding: .5em;
            background-color: #218380;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button[type="submit"]:hover {
            background-color: #1c6b63;
        }

        .login {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5em;
            margin-top: 1em;
        }

        .login p {
            color: #333;
        }

        .login a {
            color: #218380;
            font-weight: bold;
            text-decoration: none;
        }

        .login a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="form-container">
            <h2>Cadastre-se</h2>
            <form action="backend/auth/cadastro.php" method="POST">
                <input type="hidden" name="_csrf" value="<?php echo $_SESSION['_csrf']; ?>">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Seu nome:" required>
                </div>
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="Seu E-mail:" required>
                </div>
                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Sua senha:" required>
                </div>
                <button type="submit">Cadastrar</button>
            </form>
            <div class="login">
                <p>Já tem conta?</p>
                <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>

</html>