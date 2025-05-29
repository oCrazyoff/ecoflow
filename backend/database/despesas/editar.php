<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $status = $_POST['status'];
    $recorrente = $_POST['recorrente'];
    $data = $_POST['data'];
    $user_id = $_SESSION['id'];
    $id = $_POST['id'];
    $mes = $_POST['mes'];

    if (
        trim($descricao) === '' ||
        trim($status) === '' ||
        trim($recorrente) === '' ||
        trim($data) === ''
    ) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/despesas.php");
        exit();
    }


    // Atualiza a despesa no banco de dados
    $sql = "UPDATE despesas SET descricao = ?, valor = ?, status = ?, recorrente = ?, data = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssii", $descricao, $valor, $status, $recorrente, $data, $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Despesa editada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao editar despesa.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/despesas.php?mes=$mes");
