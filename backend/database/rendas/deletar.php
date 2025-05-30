<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $descricao = $_POST['descricao'];
    $user_id = $_SESSION['id'];
    $mes = $_POST['mes'];

    $mes_anterior = (date('m') == '01' ? 12 : date('m') - 1);

    // Verifica se era recorrente
    $sql_recorrente = "SELECT recorrente, id FROM rendas WHERE user_id = ? AND descricao = ? AND MONTH(data) = ?";
    $stmt_recorrente = $conn->prepare($sql_recorrente);
    $stmt_recorrente->bind_param("iss", $user_id, $descricao, $mes_anterior);
    $stmt_recorrente->execute();
    $result = $stmt_recorrente->get_result();
    $row = $result->fetch_assoc();

    if ($row && $row['recorrente'] == '1') {
        // Atualiza o valor do mês passado para não recorrente
        $sql_atualizar_recorrente = "UPDATE rendas SET recorrente = '0' WHERE descricao = ? AND user_id = ? AND MONTH(data) = ?";
        $stmt_atualizar_recorrente = $conn->prepare($sql_atualizar_recorrente);
        $stmt_atualizar_recorrente->bind_param("sis", $descricao, $user_id, $mes_anterior);
        $stmt_atualizar_recorrente->execute();
    }

    // Deleta a despesa no banco de dados
    $sql = "DELETE FROM rendas WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Renda deletada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao deletar renda.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/rendas.php?mes=$mes");
