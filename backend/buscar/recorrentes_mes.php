<?php
require_once __DIR__ . '/../valida.php';
header('Content-Type: application/json; charset=utf-8');

$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : null;
$m = filter_input(INPUT_GET, 'm', FILTER_VALIDATE_INT);

if (!$tipo || !in_array($tipo, ['despesas', 'rendas']) || !$m || $m < 1 || $m > 12) {
    echo json_encode(['sucesso' => false, 'erro' => 'Parâmetros inválidos.']);
    exit;
}

$usuario_id = $_SESSION['id'];
$itens = [];

if ($tipo === 'despesas') {
    $sql = "SELECT d.id, d.descricao, d.valor, d.categoria_id, c.nome as nome_categoria, d.data 
            FROM despesas d 
            LEFT JOIN categorias c ON d.categoria_id = c.id 
            WHERE d.usuario_id = ? AND d.recorrente = 1 AND d.tipo = 0 AND MONTH(d.data) = ? AND YEAR(d.data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $m);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['ja_adiantado'] = false;
        
        $prox_mes = $m == 12 ? 1 : $m + 1;
        $ano_prox = $m == 12 ? (int)date('Y') + 1 : (int)date('Y');
        
        $sql_check = "SELECT id FROM despesas WHERE usuario_id = ? AND descricao = ? AND MONTH(data) = ? AND YEAR(data) = ?";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bind_param("isii", $usuario_id, $row['descricao'], $prox_mes, $ano_prox);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();
        
        if ($res_check->num_rows > 0) {
            $row['ja_adiantado'] = true;
        }
        $stmt_check->close();
        
        $itens[] = $row;
    }
    $stmt->close();
} else if ($tipo === 'rendas') {
    $sql = "SELECT id, descricao, valor, data FROM rendas WHERE usuario_id = ? AND recorrente = 1 AND MONTH(data) = ? AND YEAR(data) = YEAR(CURDATE())";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $m);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['ja_adiantado'] = false;
        
        $prox_mes = $m == 12 ? 1 : $m + 1;
        $ano_prox = $m == 12 ? (int)date('Y') + 1 : (int)date('Y');
        
        $sql_check = "SELECT id FROM rendas WHERE usuario_id = ? AND descricao = ? AND MONTH(data) = ? AND YEAR(data) = ?";
        $stmt_check = $conexao->prepare($sql_check);
        $stmt_check->bind_param("isii", $usuario_id, $row['descricao'], $prox_mes, $ano_prox);
        $stmt_check->execute();
        $res_check = $stmt_check->get_result();
        
        if ($res_check->num_rows > 0) {
            $row['ja_adiantado'] = true;
        }
        $stmt_check->close();
        
        $itens[] = $row;
    }
    $stmt->close();
}

echo json_encode(['sucesso' => true, 'itens' => $itens]);
