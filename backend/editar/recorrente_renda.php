<?php
require_once __DIR__ . '/../valida.php';

$dados = json_decode(file_get_contents("php://input"), true);
$id = $dados['id'] ?? NULL;
$usuario_id = $_SESSION['id'];

// Busca o estado atual da renda
$sql = "SELECT recorrente, descricao, valor, recorrencia_grupo FROM rendas WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(["sucesso" => false, "mensagem" => "Renda não encontrada."]);
    exit;
}

$novo_recorrente = $row['recorrente'] == 0 ? 1 : 0;

// Se estiver tornando recorrente, gera ou herda o UUID do grupo
$recorrencia_grupo = null;
if ($novo_recorrente == 1) {
    $sqlGrupo = "SELECT recorrencia_grupo FROM rendas WHERE usuario_id = ? AND descricao = ? AND valor = ? AND recorrente = 1 AND recorrencia_grupo IS NOT NULL LIMIT 1";
    $stmtGrupo = $conexao->prepare($sqlGrupo);
    $stmtGrupo->bind_param("isd", $usuario_id, $row['descricao'], $row['valor']);
    $stmtGrupo->execute();
    $resGrupo = $stmtGrupo->get_result();
    $grupoExistente = $resGrupo->fetch_assoc();
    $stmtGrupo->close();

    if ($grupoExistente) {
        $recorrencia_grupo = $grupoExistente['recorrencia_grupo'];
    } else {
        $recorrencia_grupo = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

$sql2 = "UPDATE rendas SET recorrente = ?, recorrencia_grupo = ? WHERE id = ? AND usuario_id = ?";
$stmt2 = $conexao->prepare($sql2);
$stmt2->bind_param("isii", $novo_recorrente, $recorrencia_grupo, $id, $usuario_id);
$stmt2->execute();
$stmt2->close();
limparInsightsCache();

echo json_encode([
    "sucesso" => true,
    "novo_recorrente" => $novo_recorrente
]);
