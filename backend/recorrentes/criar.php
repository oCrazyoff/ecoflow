<?php
require_once __DIR__ . '/../valida.php';
require_once __DIR__ . '/materializar.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isAjax()) {
    responderJSON(false, 'Requisição inválida');
}

// CSRF (se aplicável ao projeto atual, deixamos a verificação)
// validarCSRF(); // descomente ou implemente de acordo com a validação do app

$userId = $_SESSION['id'];
$tipo = trim(strip_tags($_POST['tipo'] ?? ''));
$descricao = trim(strip_tags($_POST['descricao'] ?? ''));
$valor = trim(strip_tags($_POST['valor'] ?? ''));
$categoria_id = isset($_POST['categoria_id']) && $_POST['categoria_id'] !== '' ? (int)$_POST['categoria_id'] : null;
$dia_vencimento = (int)($_POST['dia_vencimento'] ?? 1);
$data_inicio = trim(strip_tags($_POST['data_inicio'] ?? date('Y-m-d')));

// Validações básicas
if (!in_array($tipo, ['renda', 'despesa'])) {
    responderJSON(false, 'Tipo inválido.');
}
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

if ($tipo === 'despesa' && !$categoria_id) {
    responderJSON(false, 'A categoria é obrigatória para despesas.');
}

// Inserir template
$sql = "INSERT INTO recorrentes (usuario_id, tipo, descricao, valor, categoria_id, dia_vencimento, ativo, data_inicio) 
        VALUES (?, ?, ?, ?, ?, ?, 1, ?)";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("issdiis", $userId, $tipo, $descricao, $valorFormatado, $categoria_id, $dia_vencimento, $data_inicio);

if ($stmt->execute()) {
    $novoId = $stmt->insert_id;
    $stmt->close();
    
    // Materializar para o mês atual imediatamente
    $mesAtual = (int)date('m');
    $anoAtual = (int)date('Y');
    materializarRecorrentes($userId, $mesAtual, $anoAtual);
    
    if (function_exists('limparInsightsCache')) {
        limparInsightsCache($userId);
    }
    
    responderJSON(true, 'Recorrência criada com sucesso.', ['id' => $novoId]);
} else {
    $stmt->close();
    responderJSON(false, 'Erro ao criar recorrência no banco de dados.');
}
