<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $frequencia = $_POST['frequencia'];
    $tipo = $_POST['tipo'];
    $user_id = $_SESSION['id'];
    $id = $_POST['id'];

    // Verifica se os campos estão preenchidos
    if (empty($descricao) || empty($valor) || empty($frequencia) || empty($tipo)) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/cadastro/despesa.php");
        exit();
    }

    // Edita a despesa no banco de dados
    $sql = "UPDATE despesas SET descricao = ?, valor = ?, frequencia = ?, tipo = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdssii", $descricao, $valor, $frequencia, $tipo, $id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = "Despesa editada com sucesso.";
    } else {
        $_SESSION['resposta'] = "Erro ao editar despesa.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/despesas.php");
