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
    <?php include("../backend/includes/menu.php") ?>
    <div class="main-content">
        <h1><i class="bi bi-person-gear"></i> Configurações do Usuário</h1>

        <form id="configForm" action="../backend/database/usuario/editar.php" method="POST">
            <div class="form-section">
                <h2><i class="bi bi-person-lines-fill"></i> Informações da Conta</h2>

                <div class="form-group">
                    <label for="nome"><i class="bi bi-person"></i> Nome de Usuário:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo $row['nome']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="bi bi-envelope"></i> Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="bi bi-lock"></i> Segurança</h2>

                <div class="form-group senha-wrapper">
                    <label for="password"><i class="bi bi-lock-fill"></i> Senha:</label>
                    <div class="senha-container">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <i class="bi bi-eye toggle-password" onclick="togglePassword(this)"></i>
                    </div>
                </div>
            </div>

            <div class="botoes">
                <button type="submit" id="salvarBtn" disabled><i class="bi bi-save"></i> Salvar</button>
                <a href="../backend/auth/logout.php"><i class="bi bi-box-arrow-left"></i> Trocar conta</a>
            </div>
        </form>

        <div id="mensagem-status" class="mensagem-status oculto">
            <i class="bi bi-check-circle-fill"></i> Alterações salvas com sucesso!
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

        // Habilitar botão salvar somente se houver alterações
        const form = document.getElementById('configForm');
        const salvarBtn = document.getElementById('salvarBtn');
        const originalData = new FormData(form);

        form.addEventListener('input', () => {
            const currentData = new FormData(form);
            let changed = false;
            for (let [key, value] of currentData.entries()) {
                if (value !== originalData.get(key)) {
                    changed = true;
                    break;
                }
            }
            salvarBtn.disabled = !changed;
        });
    </script>
</body>

</html>