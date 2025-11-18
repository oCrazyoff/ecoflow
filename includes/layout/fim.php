</body>
<?php include __DIR__ . "/../div_erro.php"; ?>
<script src="<?= BASE_URL . "assets/js/toggle_menu.js" ?>"></script>
<script src="<?= BASE_URL . "assets/js/loading.js" ?>"></script>
<?php
// buscando avisos pendentes
$sql = "SELECT id, titulo, conteudo FROM avisos";
$stmt = $conexao->prepare($sql);
$stmt->execute();
$avisos = $stmt->get_result();
$stmt->close();

while ($aviso = $avisos->fetch_assoc()):
    // verificando se o aviso ja foi visto
    $sql_verificar = "SELECT 1 FROM usuarios_avisos_vistos WHERE usuario_id = ? AND aviso_id = ?";
    $stmt_verificar = $conexao->prepare($sql_verificar);
    $stmt_verificar->bind_param("ii", $_SESSION['id'], $aviso['id']);
    $stmt_verificar->execute();
    $resultado_verificar = $stmt_verificar->get_result();
    $stmt_verificar->close();

    if ($resultado_verificar->num_rows <= 0):
?>
<script src="<?= BASE_URL . "assets/js/aviso.js" ?>"></script>
<?php endif; ?>
<?php endwhile; ?>

</html>