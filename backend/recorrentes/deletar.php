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

// Verifica ownership
$chk = $conexao->prepare("SELECT id, tipo FROM recorrentes WHERE id = ? AND usuario_id = ?");
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

// Primeiro, limpa os lançamentos futuros gerados por essa recorrência (após o mês atual)
$ultimoDiaMesAtual = date('Y-m-t');
if ($tipo === 'despesa') {
    $sqlLimpa = "DELETE FROM despesas WHERE recorrente_id = ? AND usuario_id = ? AND data > ? AND status = 0";
} else {
    $sqlLimpa = "DELETE FROM rendas WHERE recorrente_id = ? AND usuario_id = ? AND data > ?";
}
$stmtL = $conexao->prepare($sqlLimpa);
$stmtL->bind_param("iis", $id, $userId, $ultimoDiaMesAtual);
$stmtL->execute();
$stmtL->close();

// Deletar o template em si
$stmt = $conexao->prepare("DELETE FROM recorrentes WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $userId);

if ($stmt->execute()) {
    $stmt->close();
    
    if (function_exists('limparInsightsCache')) {
        limparInsightsCache($userId);
    }
    
    responderJSON(true, 'Recorrência excluída com sucesso.');
} else {
    $stmt->close();
    responderJSON(false, 'Erro ao excluir recorrência.');
}
