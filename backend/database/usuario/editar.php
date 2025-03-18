<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verifica se os campos estão preenchidos
    if (empty($nome) || empty($email)) {
        $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../../../pages/user_config.php");
        exit();
    }

    // Verifica se o usuário existe
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Atualiza os dados do usuário
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $nome, $email, $id);
        } else {
            $_SESSION['resposta'] = "Informe a senha para atualizar o usuário.";
            header("Location: ../../../pages/user_config.php");
            exit();
        }

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Atualizado com sucesso.";

            // Atualiza a sessão com os novos dados
            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $email;
            $_SESSION['senha'] = $passwordHash;
        } else {
            $_SESSION['resposta'] = "Erro ao atualizar o usuário.";
        }
    } else {
        $_SESSION['resposta'] = "Usuário não encontrado.";
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/user_config.php");
exit();
$conn->close();
