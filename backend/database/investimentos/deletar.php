<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $user_id = $_SESSION['id'];

    // Deleta a despesa no banco de dados
    $sql = "DELETE FROM investimentos WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Investimento deletado com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao deletar investimento.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/investimentos.php");
