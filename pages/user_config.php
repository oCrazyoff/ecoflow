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
        <form action="../backend/database/usuario/editar.php" method="POST">
            <div class="form-group">
                <label for="nome"><i class="bi bi-person"></i> Nome de Usuário:</label>
                <input type="text" id="nome" name="nome" value="<?php echo $row['nome']; ?>" required>
            </div>
            <div class="form-group">
                <label for="email"><i class="bi bi-envelope"></i> Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="bi bi-lock"></i> Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="botoes">
                <button type="submit"><i class="bi bi-save"></i> Salvar</button>
                <a href="../backend/auth/logout.php"><i class="bi bi-box-arrow-left"></i> Trocar conta</a>
            </div>
        </form>
    </div>
    <script>
        <?php
        if (isset($_SESSION['resposta'])) {
            echo "alert('" . $_SESSION['resposta'] . "');";
            unset($_SESSION['resposta']);
        }
        ?>
    </script>
</body>

</html>