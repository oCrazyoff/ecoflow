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
    $vencimento = $_POST['vencimento'];
    $user_id = $_SESSION['id'];
    $id = $_POST['id'];

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($ticker) || empty($rendimento) || empty($frequencia) || empty($tipo_investimento) || empty($custo) || empty($vencimento)) {
        $_SESSION['resposta'] = "Preencha todos os campos.";
        header("Location: ../../../pages/cadastro/investimento.php");
        exit();
    }

    $dataObj = DateTime::createFromFormat('Y-m-d', $vencimento);
    if (!$dataObj) {
        $_SESSION['resposta'] = "Data de vencimento inválida.";
        header("Location: ../../../pages/cadastro/investimento.php");
        exit();
    }

    $vencimento = $dataObj->format('Y-m-d');

    // Insere o investimento no banco de dados
    $sql = "UPDATE investimentos SET nome = ?, ticker = ?, rendimento = ?, frequencia = ?, tipo = ?, custo = ?, vencimento = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssdii", $nome, $ticker, $rendimento, $frequencia, $tipo_investimento, $custo, $vencimento, $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Investimento editado com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao editar investimento.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/investimentos.php");
