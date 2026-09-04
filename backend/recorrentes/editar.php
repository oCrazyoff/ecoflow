<?php
require_once __DIR__ . '/../valida.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isAjax()) {
    responderJSON(false, 'Requisição inválida');
}

$userId = $_SESSION['id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if (!$id) {
    responderJSON(false, 'ID não fornecido.');
}

$descricao = trim(strip_tags($_POST['descricao'] ?? ''));
$valor = trim(strip_tags($_POST['valor'] ?? ''));
$categoria_id = isset($_POST['categoria_id']) && $_POST['categoria_id'] !== '' ? (int)$_POST['categoria_id'] : null;
$dia_vencimento = (int)($_POST['dia_vencimento'] ?? 1);
$data_fim = isset($_POST['data_fim']) && !empty($_POST['data_fim']) ? trim(strip_tags($_POST['data_fim'])) : null;

if (empty($descricao)) {
    responderJSON(false, 'A descrição é obrigatória.');
}
$valorFormatado = (float)str_replace(['.', ','], ['', '.'], $valor);
if ($valorFormatado <= 0) {
    responderJSON(false, 'O valor deve ser maior que zero.');
}
if ($dia_vencimento < 1 || $dia_vencimento > 31) {
    responderJSON(false, 'Dia de vencimento inválido.');
}

// Verifica ownership
$chk = $conexao->prepare("SELECT tipo FROM recorrentes WHERE id = ? AND usuario_id = ?");
$chk->bind_param("ii", $id, $userId);
$chk->execute();
$res = $chk->get_result();
if ($res->num_rows === 0) {
    $chk->close();
    responderJSON(false, 'Recorrência não encontrada ou sem permissão.');
}
$row = $res->fetch_assoc();
$tipo = $row['tipo'];
$chk->close();

if ($tipo === 'despesa' && !$categoria_id) {
    responderJSON(false, 'A categoria é obrigatória para despesas.');
}

$sql = "UPDATE recorrentes SET descricao = ?, valor = ?, categoria_id = ?, dia_vencimento = ?, data_fim = ? WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("sdiisii", $descricao, $valorFormatado, $categoria_id, $dia_vencimento, $data_fim, $id, $userId);

if ($stmt->execute()) {
    $stmt->close();
    
    if (function_exists('limparInsightsCache')) {
        limparInsightsCache($userId);
    }
    
    responderJSON(true, 'Recorrência atualizada com sucesso.');
} else {
    $stmt->close();
    responderJSON(false, 'Erro ao atualizar recorrência.');
}
