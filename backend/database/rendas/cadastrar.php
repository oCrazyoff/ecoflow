<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $tipo = $_POST['tipo'];
    $data = $_POST['data']; // Capturar a data enviada pelo formulário
    $id = $_SESSION['id'];

    // Verifica se os campos estão preenchidos
    if (empty($descricao) || empty($valor) || empty($tipo) || empty($data)) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/cadastro/renda.php");
        exit();
    }

    // Insere a renda no banco de dados
    $sql = "INSERT INTO rendas (descricao, valor, tipo, data, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssi", $descricao, $valor, $tipo, $data, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Renda cadastrada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar renda.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/rendas.php");
