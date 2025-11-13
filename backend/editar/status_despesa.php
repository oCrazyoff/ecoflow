<?php
require_once __DIR__ . '/../valida.php';

$dados = json_decode(file_get_contents("php://input"), true);
$id = $dados['id'] ?? NULL;

$sql = "SELECT status FROM despesas WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();

$novo_status = $row['status'] == 0 ? 1 : 0;

$sql2 = "UPDATE despesas SET status = ? WHERE id = ?";
$stmt2 = $conexao->prepare($sql2);
$stmt2->bind_param("ii", $novo_status, $id);
$stmt2->execute();

echo json_encode([
    "sucesso" => true,
    "novo_status" => $novo_status
]);
