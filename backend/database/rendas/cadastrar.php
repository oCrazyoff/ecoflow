<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $frequencia = $_POST['frequencia'];
    $tipo = $_POST['tipo'];
    $id = $_SESSION['id'];

    // Verifica se os campos estão preenchidos
    if (empty($descricao) || empty($valor) || empty($frequencia) || empty($tipo)) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/cadastro/renda.php");
        exit();
    }

    // Insere a despesa no banco de dados
    $sql = "INSERT INTO rendas (descricao, valor, frequencia, tipo, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssi", $descricao, $valor, $frequencia, $tipo, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Renda cadastrada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar renda.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/rendas.php");
