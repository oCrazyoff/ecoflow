<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_investimento'];
    $ticker = $_POST['ticker'];
    $rendimento = $_POST['rendimento'];
    $frequencia = $_POST['frequencia'];
    $tipo_investimento = $_POST['tipo_investimento'];
    $custo = $_POST['custo'];
    $id = $_SESSION['id'];

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($ticker) || empty($rendimento) || empty($frequencia) || empty($tipo_investimento) || empty($custo)) {
        $_SESSION['resposta'] = "Preencha todos os campos.";
        header("Location: ../../../pages/cadastro/investimento.php");
        exit();
    }

    // Insere o investimento no banco de dados
    $sql = "INSERT INTO investimentos (nome, ticker, rendimento, frequencia, tipo, custo, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nome, $ticker, $rendimento, $frequencia, $tipo_investimento, $custo, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Investimento cadastrado com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar investimento.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/investimentos.php");
