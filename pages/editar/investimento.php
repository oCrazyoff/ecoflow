<?php
require_once("../../backend/includes/valida.php");
require_once("../../backend/config/database.php");

$id = $_POST['id'];

$sql = "SELECT * FROM investimentos WHERE id = ? AND user_id = ?";
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
    <title>Editar <?php echo $row['nome'] ?></title>
    <link rel="stylesheet" href="../../assets/css/form.css?v=<?php echo time(); ?>">
    <?php include("../../backend/includes/head.php") ?>
</head>

<body>
    <?php include("../../backend/includes/loading.php") ?>
    <?php include("../../backend/includes/menu.php") ?>
    <div class="main-content">
        <h2><?php echo $row['nome'] ?></h2>
        <button onclick="window.history.back()" id="btn-voltar">
            <i class="bi bi-arrow-left"></i>
        </button>

        <div class="form-container">
            <form action="../../backend/database/investimentos/editar.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $row['id'] ?>">
                <div class="top-form">
                    <!-- Informações Gerais -->
                    <div class="card">
                        <h3>Informações Gerais</h3>
                        <div class="form-group">
                            <label for="nome_investimento">Nome do Investimento</label>
                            <input type="text" id="nome_investimento" name="nome_investimento"
                                value="<?php echo $row['nome'] ?>" required>
                        </div>
                    </div>

                    <!-- Tipo de Investimento -->
                    <div class="card">
                        <h3>Tipo de Investimento</h3>
                        <div class="form-group">
                            <label for="tipo_investimento">Tipo</label>
                            <select id="tipo_investimento" name="tipo_investimento" required>
                                <option value="FII" <?php echo ($row['tipo'] == 'FII') ? 'selected' : ''; ?>>FII
                                </option>
                                <option value="Ação" <?php echo ($row['tipo'] == 'Ação') ? 'selected' : ''; ?>>Ação
                                </option>
                                <option value="Renda Fixa"
                                    <?php echo ($row['tipo'] == 'Renda Fixa') ? 'selected' : ''; ?>>Renda Fixa</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custos, Rendimentos e Data -->
                    <div class="card">
                        <h3>Custos e Rendimentos</h3>
                        <div class="form-group">
                            <label for="custo">Custo</label>
                            <input type="number" id="custo" name="custo" value="<?php echo $row['custo'] ?>" step="0.01"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="recorrente">Recorrente</label>
                            <select id="recorrente" name="recorrente" required>
                                <option value="Sim" <?php echo ($row['recorrente'] == '1' ? 'selected' : '') ?>>Sim
                                </option>
                                <option value="Não" <?php echo ($row['recorrente'] == '0' ? 'selected' : '') ?>>
                                    Não
                                </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="data">Data</label>
                            <input type="date" id="data" name="data"
                                value="<?php echo date('Y-m-d', strtotime($row['data'])); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Botão de Envio -->
                <div class="container-btn">
                    <button type="submit">Editar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const hoje = new Date().toISOString().split('T')[0];
        document.getElementById('vencimento').value = hoje;
    </script>
</body>

</html>