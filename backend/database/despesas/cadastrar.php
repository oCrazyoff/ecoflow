<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $status = $_POST['status'];
    $recorrente = $_POST['recorrente'];
    $data = $_POST['data'];
    $id = $_SESSION['id'];
    $mes = $_POST['mes'];

    // Verifica se os campos estão preenchidos
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

    // Insere a despesa no banco de dados
    $sql = "INSERT INTO despesas (descricao, valor, status, recorrente, data, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssi", $descricao, $valor, $status, $recorrente, $data, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Despesa cadastrada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar despesa.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/despesas.php?mes=$mes");
