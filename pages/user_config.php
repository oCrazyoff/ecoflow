<?php
require_once("../backend/includes/valida.php");
require_once("../backend/config/database.php");

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

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
    <?php include("../backend/includes/loading.php") ?>
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <h1>Configurações do Usuário</h1>
        <form action="../backend/database/usuario/editar.php" method="POST">
            <h2>Informações Pessoais</h2>
            <div class="input-group">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>"
                    required>
            </div>
            <input type="hidden" name="info-pessoal" value="1">
            <button type="submit">Salvar alterações</button>
        </form>
        <form action="../backend/database/usuario/editar.php" method="POST">
            <h2>Alterar Senha</h2>
            <div class="input-group">
                <label for="senha-atual">Senha atual</label>
                <input type="password" id="senha-atual" name="senha-atual" required>
            </div>
            <div class="input-group">
                <label for="nova-senha">Nova senha</label>
                <input type="password" id="nova-senha" name="nova-senha" required>
            </div>
            <div class="input-group">
                <label for="nova-senha">Confirmar senha</label>
                <input type="password" id="confirmar-senha" name="confirmar-senha" required>
            </div>
            <input type="hidden" name="info-senha" value="1">
            <button type="submit">Atualizar senha</button>
        </form>
        <div class="container-deslogar">
            <h2>Sair da conta</h2>
            <p>Ao sair da conta, você precisará fazer login novamente para acessar o sistema.</p>
            <a href="../backend/auth/logout.php"><i class="bi bi-arrow-bar-right"></i> Sair da conta</a>
        </div>
    </div>

    <?php include("../backend/includes/div_erro.php") ?>

    <script>
        // Mostrar/ocultar senha
        function togglePassword(icon) {
            const input = icon.previousElementSibling;
            input.type = input.type === "password" ? "text" : "password";
            icon.classList.toggle("bi-eye");
            icon.classList.toggle("bi-eye-slash");
        }
    </script>
</body>

</html>