<?php
require_once __DIR__ . '/../valida.php';

$dados = json_decode(file_get_contents("php://input"), true);
$id = $dados['id'] ?? NULL;
$usuario_id = $_SESSION['id'];

// Busca a despesa com validação de propriedade
$sql = "SELECT status, tipo, adiantamento_ref_id FROM despesas WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(["sucesso" => false, "mensagem" => "Despesa não encontrada."]);
    exit;
}

// Bloquear toggle em despesas adiantadas
if ($row['tipo'] == 1) {
    echo json_encode(["sucesso" => false, "mensagem" => "Lançamentos de adiantamento não podem ter o status alterado."]);
    exit;
}

if ($row['status'] == 2) {
    echo json_encode(["sucesso" => false, "mensagem" => "Despesas pagas antecipadamente não podem ter o status alterado. Cancele o adiantamento primeiro."]);
    exit;
}

$novo_status = $row['status'] == 0 ? 1 : 0;

// Atualizar status e data_pagamento
$data_pagamento = ($novo_status == 1) ? date('Y-m-d') : null;

$sql2 = "UPDATE despesas SET status = ?, data_pagamento = ? WHERE id = ? AND usuario_id = ?";
$stmt2 = $conexao->prepare($sql2);
$stmt2->bind_param("isii", $novo_status, $data_pagamento, $id, $usuario_id);
$stmt2->execute();
$stmt2->close();
limparInsightsCache();

echo json_encode([
    "sucesso" => true,
    "novo_status" => $novo_status
]);
