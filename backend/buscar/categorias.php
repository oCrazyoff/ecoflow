<?php
require_once __DIR__ . '/../valida.php';
header('Content-Type: application/json');

// Pega o ID via GET e valida
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['erro' => 'ID inválido']);
    exit;
} else {
    $usuario_id = $_SESSION['id'];
    $stmt = $conexao->prepare("SELECT nome FROM categorias WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode(['erro' => 'Categoria não encontrada']);
        exit;
    }

    $row = $resultado->fetch_assoc();

    // Retorna o JSON com os dados
    echo json_encode($row);

    $stmt->close();
}
