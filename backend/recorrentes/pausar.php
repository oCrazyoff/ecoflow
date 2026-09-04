<?php
require_once __DIR__ . '/../valida.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isAjax()) {
    responderJSON(false, 'Requisição inválida');
}

$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? (int)$input['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);
$userId = $_SESSION['id'];

if (!$id) {
    responderJSON(false, 'ID não fornecido.');
}

// Verifica ownership e estado atual
$chk = $conexao->prepare("SELECT ativo FROM recorrentes WHERE id = ? AND usuario_id = ?");
$chk->bind_param("ii", $id, $userId);
$chk->execute();
$res = $chk->get_result();

if ($res->num_rows === 0) {
    $chk->close();
    responderJSON(false, 'Recorrência não encontrada ou sem permissão.');
}
$row = $res->fetch_assoc();
$chk->close();

$novoEstado = $row['ativo'] ? 0 : 1;

$stmt = $conexao->prepare("UPDATE recorrentes SET ativo = ? WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("iii", $novoEstado, $id, $userId);

if ($stmt->execute()) {
    $stmt->close();
    
    if (function_exists('limparInsightsCache')) {
        limparInsightsCache($userId);
    }
    
    responderJSON(true, 'Status atualizado com sucesso.', ['ativo' => $novoEstado]);
} else {
    $stmt->close();
    responderJSON(false, 'Erro ao atualizar status.');
}
