<?php
$tipo = "";
$nome = "";
$custo = "";
$data = (new DateTime('now', new DateTimeZone('America/Sao_Paulo')))->format('Y-m-d');
$recorrente = '';
$action = "cadastrar.php";
$botao = "Cadastrar";
$id_editar = "";

if (isset($_GET['editar']) && isset($_GET['id'])) {
    $id_editar = $_GET['id'];

    $sql = "SELECT * FROM investimentos WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_editar, $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        $tipo = $row['tipo'];
        $nome = $row['nome'];
        $custo = $row['custo'];
        $data = date('Y-m-d', strtotime($row['data']));
        $recorrente = $row['recorrente'];
        $action = "editar.php";
        $botao = "Editar";
    } else {
        exit('Investimento não encontrado ou acesso não autorizado.');
    }
}
?>

<div id="overlay" onclick="window.location.href = window.location.pathname"></div>
<div class="form-container" id="form-alert">
    <h2><?php echo htmlspecialchars($botao) ?> Investimento</h2>
    <p>Preencha os dados abaixo para <?php echo strtolower(htmlspecialchars($botao)) ?> um investimento.</p>

    <form action="../backend/database/investimentos/<?php echo htmlspecialchars($action) ?>" method="POST">
        <?php if ($id_editar): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_editar) ?>">
        <?php endif; ?>
        <input type="hidden" name="mes" value="<?= htmlspecialchars($mes_selecionado) ?>">
        <div class="top-form">
            <div class="form-group">
                <label for="tipo">Tipo</label>
                <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($tipo) ?>" required>
            </div>
            <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome) ?>" required>
            </div>
            <div class="form-group">
                <label for="custo">Custo</label>
                <input type="number" id="custo" name="custo" step="0.01" value="<?php echo htmlspecialchars($custo) ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="data">Data</label>
                <input type="date" id="data" name="data" value="<?php echo htmlspecialchars($data) ?>" required>
            </div>
            <div class="form-group">
                <label for="recorrente">Recorrente</label>
                <select id="recorrente" name="recorrente" required>
                    <option value="1" <?php echo ($recorrente == 1 ? 'selected' : '') ?>>Sim</option>
                    <option value="0" <?php echo ($recorrente == 0 ? 'selected' : '') ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="container-btn">
            <button type="button" id="btn-cancelar"
                onclick="window.location.href = window.location.pathname">Cancelar</button>
            <button type="submit"><?php echo htmlspecialchars($botao) ?></button>
        </div>
    </form>
</div>

<script>
    function abrirForm() {
        const form = document.getElementById('form-alert');
        const overlay = document.getElementById('overlay');
        form.style.display = 'block';
        overlay.style.display = 'block';
    }

    function fecharForm() {
        const form = document.getElementById('form-alert');
        const overlay = document.getElementById('overlay');
        form.style.display = 'none';
        overlay.style.display = 'none';
    }

    <?php
    if (isset($_GET['editar']) && isset($_GET['id'])) {
        echo 'abrirForm();';
    }
    ?>
</script>