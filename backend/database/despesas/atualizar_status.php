<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $data = $_POST['data'];
    $user_id = $_SESSION['id'];
    $id = $_POST['id'];
    $mes = $_POST['mes'];

    // Atualiza a despesa no banco de dados
    $sql = "UPDATE despesas SET status = ?, data = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $status, $data, $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Despesa atualizada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao editar status.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/despesas.php?mes=$mes");
