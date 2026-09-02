<?php
require_once __DIR__ . '/../valida.php';
header('Content-Type: application/json');

// Pega o ID via GET e valida
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: (isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : false);

if (!$id) {
    echo json_encode(['erro' => 'ID inválido']);
    exit;
} else {
    $usuario_id = $_SESSION['id'];
    $stmt = $conexao->prepare("SELECT descricao, valor, recorrente, DATE(data) AS data, categoria_id, status, parcela_grupo, parcela_numero, parcela_total FROM despesas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode(['erro' => 'Despesa não encontrada']);
        exit;
    }

    $row = $resultado->fetch_assoc();
    $stmt->close();

    // Se a despesa for parcelada, busca a data da última parcela do grupo
    if (!empty($row['parcela_grupo'])) {
        $stmt_last = $conexao->prepare("SELECT DATE(data) AS ultima_data FROM despesas WHERE parcela_grupo = ? AND usuario_id = ? ORDER BY parcela_numero DESC LIMIT 1");
        if ($stmt_last) {
            $stmt_last->bind_param("si", $row['parcela_grupo'], $usuario_id);
            $stmt_last->execute();
            $res_last = $stmt_last->get_result();
            if ($last = $res_last->fetch_assoc()) {
                $row['parcela_ultima_data'] = $last['ultima_data'];
            }
            $stmt_last->close();
        }
    }

    // Retorna o JSON com os dados
    echo json_encode($row);
}
