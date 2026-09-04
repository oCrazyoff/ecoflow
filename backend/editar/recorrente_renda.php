<?php
require_once __DIR__ . '/../valida.php';

$dados = json_decode(file_get_contents("php://input"), true);
$id = $dados['id'] ?? NULL;
$usuario_id = $_SESSION['id'];

// Busca o estado atual da renda
$sql = "SELECT recorrente, descricao, valor, data, recorrente_id FROM rendas WHERE id = ? AND usuario_id = ?";
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
$recorrente_id = $row['recorrente_id'];

if ($novo_recorrente == 1) {
    // Liga recorrência: cria novo template
    $dia_vencimento = (int)date('d', strtotime($row['data']));
    $sqlTemplate = "INSERT INTO recorrentes (usuario_id, tipo, descricao, valor, dia_vencimento, data_inicio) VALUES (?, 'renda', ?, ?, ?, ?)";
    $stmtT = $conexao->prepare($sqlTemplate);
    $stmtT->bind_param("isdis", $usuario_id, $row['descricao'], $row['valor'], $dia_vencimento, $row['data']);
    $stmtT->execute();
    $recorrente_id = $stmtT->insert_id;
    $stmtT->close();
} else {
    // Desliga recorrência: pausa template e limpa ID
    if ($recorrente_id) {
        $sqlPause = "UPDATE recorrentes SET ativo = 0 WHERE id = ? AND usuario_id = ?";
        $stmtP = $conexao->prepare($sqlPause);
        $stmtP->bind_param("ii", $recorrente_id, $usuario_id);
        $stmtP->execute();
        $stmtP->close();
    }
    $recorrente_id = null;
}

$sql2 = "UPDATE rendas SET recorrente = ?, recorrente_id = ? WHERE id = ? AND usuario_id = ?";
$stmt2 = $conexao->prepare($sql2);
$stmt2->bind_param("iiii", $novo_recorrente, $recorrente_id, $id, $usuario_id);
$stmt2->execute();
$stmt2->close();
limparInsightsCache();

echo json_encode([
    "sucesso" => true,
    "novo_recorrente" => $novo_recorrente
]);
