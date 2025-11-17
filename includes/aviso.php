<?php
// buscando avisos pendentes
$sql = "SELECT id, titulo, conteudo FROM avisos WHERE ativo = 1";
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
<div class="container-aviso">
    <div class="aviso">
        <h2><i class="bi bi-info-lg"></i> <?= htmlspecialchars($avis['titulo']) ?></h2>
        <p><?= htmlspecialchars($aviso['conteudo']) ?></p>
        <div class="container-btn"><button onclick="vistarAviso(<?= htmlspecialchars($aviso['id']) ?>)">
                Entendido
                <i class="bi bi-check-all"></i>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
<?php endwhile; ?>