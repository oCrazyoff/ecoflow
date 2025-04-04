<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $status = $_POST['status'];
    $tipo = $_POST['tipo'];
    $data = $_POST['data'];
    $id = $_SESSION['id'];

    // Verifica se os campos estão preenchidos
    if (empty($descricao) || empty($valor) || empty($status) || empty($tipo) || empty($data)) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/cadastro/despesa.php");
        exit();
    }

    // Insere a despesa no banco de dados
    $sql = "INSERT INTO despesas (descricao, valor, status, tipo, data, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssi", $descricao, $valor, $status, $tipo, $data, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Despesa cadastrada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar despesa.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/despesas.php");