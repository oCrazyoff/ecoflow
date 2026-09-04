<?php
require_once __DIR__ . '/../valida.php';
header('Content-Type: application/json');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['erro' => 'ID inválido']);
    exit;
}

$usuario_id = $_SESSION['id'];
$stmt = $conexao->prepare("SELECT id, tipo, descricao, valor, categoria_id, dia_vencimento, DATE(data_inicio) AS data_inicio, DATE(data_fim) AS data_fim, ativo FROM recorrentes WHERE id = ? AND usuario_id = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(['erro' => 'Recorrência não encontrada']);
    exit;
}

$row = $resultado->fetch_assoc();
echo json_encode($row);
$stmt->close();
