<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $recorrente = $_POST['recorrente'];
    $tipo = $_POST['tipo'];
    $custo = $_POST['custo'];
    $data = $_POST['data'];
    $user_id = $_SESSION['id'];
    $id = $_POST['id'];

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

    // Atualiza o investimento no banco de dados
    $sql = "UPDATE investimentos SET nome = ?, recorrente = ?, tipo = ?, custo = ?, data = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $nome, $recorrente, $tipo, $custo, $data, $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Investimento editado com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao editar investimento.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/investimentos.php");
