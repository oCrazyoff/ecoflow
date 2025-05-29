<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $recorrente = $_POST['recorrente'];
    $tipo = $_POST['tipo'];
    $custo = $_POST['custo'];
    $data = $_POST['data'];
    $id = $_SESSION['id'];
    $mes = $_POST['mes'];

    // Verifica se os campos estão preenchidos
    if (
        trim($nome) === '' ||
        trim($recorrente) === '' ||
        trim($tipo) === '' ||
        trim($data) === ''
    ) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/investimentos.php");
        exit();
    }

    // Insere o investimento no banco de dados
    $sql = "INSERT INTO investimentos (nome, recorrente, tipo, custo, data, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nome, $recorrente, $tipo, $custo, $data, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Investimento cadastrado com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar investimento.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/investimentos.php?mes=$mes");
