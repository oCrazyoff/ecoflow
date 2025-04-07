<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $recorrente = $_POST['recorrente'];
    $data = $_POST['data']; // Capturar a data enviada pelo formulário
    $user_id = $_SESSION['id'];
    $id = $_POST['id'];

    // Verifica se os campos estão preenchidos
    if (empty($descricao) || empty($valor) || empty($recorrente) || empty($data)) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/editar/renda.php?id=$user_id");
        exit();
    }

    // Atualiza a renda no banco de dados
    $sql = "UPDATE rendas SET descricao = ?, valor = ?, recorrente = ?, data = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssii", $descricao, $valor, $recorrente, $data, $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Renda editada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao editar renda.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/rendas.php");
