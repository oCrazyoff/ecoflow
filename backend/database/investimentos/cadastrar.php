<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome_investimento'];
    $rendimento = $_POST['rendimento'];
    $tipo_investimento = $_POST['tipo_investimento'];
    $custo = $_POST['custo'];
    $data = $_POST['data']; // Capturar a data enviada pelo formulário
    $id = $_SESSION['id'];

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($rendimento) || empty($tipo_investimento) || empty($custo) || empty($data)) {
        $_SESSION['resposta'] = "Preencha todos os campos.";
        header("Location: ../../../pages/cadastro/investimento.php");
        exit();
    }

    // Insere o investimento no banco de dados
    $sql = "INSERT INTO investimentos (nome, rendimento, tipo, custo, data, user_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsssi", $nome, $rendimento, $tipo_investimento, $custo, $data, $id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Investimento cadastrado com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao cadastrar investimento.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/investimentos.php");
