<?php
require_once("../../backend/includes/valida.php");
require_once("../../backend/config/database.php");

$id = $_POST['id'];

$sql = "SELECT * FROM rendas WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar <?php echo $row['descricao'] ?></title>
    <link rel="stylesheet" href="../../assets/css/form.css?v=<?php echo time(); ?>">
    <?php include("../../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../../backend/includes/loading.php") ?>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2><?php echo $row['descricao'] ?></h2>
        <button onclick="window.history.back()" id="btn-voltar">
            <i class="bi bi-arrow-left"></i>
        </button>

        <div class="form-container">
            <form action="../../backend/database/rendas/editar.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                <div class="top-form">
                    <div class="card">
                        <h3>Informações da Renda</h3>
                        <div class="form-group">
                            <label for="descricao">Descrição</label>
                            <input type="text" id="descricao" name="descricao" value="<?php echo $row['descricao'] ?>"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="valor">Valor</label>
                            <input type="number" id="valor" name="valor" value="<?php echo $row['valor'] ?>" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" id="data" name="data"
                                value="<?php echo date('Y-m-d', strtotime($row['data'])); ?>" required>
                        </div>
                    </div>

                    <div class="card">
                        <h3>Configuração da Renda</h3>
                        <div class="form-group">
                            <label for="recorrente">Recorrente</label>
                            <select id="recorrente" name="recorrente" required>
                                <option value="Sim" <?php echo ($row['recorrente'] == 'Sim') ? 'selected' : ''; ?>>Sim
                                </option>
                                <option value="Não" <?php echo ($row['recorrente'] == 'Não') ? 'selected' : ''; ?>>
                                    Não
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="container-btn">
                    <button type="submit">Editar</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>