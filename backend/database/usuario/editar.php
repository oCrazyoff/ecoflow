<?php
require_once("../../config/database.php");
require_once("../../includes/valida.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_SESSION['id'];

    if ($_POST['editar'] == '1') {
        $nome = $_POST['nome'];
        $email = $_POST['email'];

        // Verifica se os campos estão preenchidos
        if (empty($nome) || empty($email)) {
            $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
            header("Location: ../../../pages/user_config.php");
            exit();
        }

        // Atualiza os dados do usuário
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Atualizado com sucesso.";

            // Atualiza a sessão com os novos dados
            $_SESSION['nome'] = $nome;
            $_SESSION['email'] = $email;
        } else {
            $_SESSION['resposta'] = "Erro ao atualizar o usuário.";
        }
    } elseif ($_POST['editar'] == '2') {
        $senha_atual = $_POST['senha-atual'];
        $nova_senha = $_POST['nova-senha'];
        $confirmar_senha = $_POST['confirmar-senha'];

        // Verifica se os campos estão preenchidos
        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
            $_SESSION['resposta'] = "Preencha todos os campos obrigatórios.";
            header("Location: ../../../pages/user_config.php");
            exit();
        }

        // Verifica se a senha esta correta
        $sql_verificar = "SELECT senha FROM usuarios WHERE id = ?";
        $stmt_verificar = $conn->prepare($sql_verificar);
        $stmt_verificar->bind_param("s", $id);
        $stmt_verificar->execute();
        $stmt_verificar->bind_result($senha_usuario);
        $stmt_verificar->fetch();
        $stmt_verificar->close();

        if (password_verify($senha_atual, $senha_usuario)) {
            // Verifica se a nova senha é igual a senha confirmada
            if ($nova_senha === $confirmar_senha) {
                // Atualiza a senha para a nova
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                $sql_senha = "UPDATE usuarios SET senha = ? WHERE id = ?";
                $stmt_senha = $conn->prepare($sql_senha);
                $stmt_senha->bind_param("si", $nova_senha_hash, $id);

                if ($stmt_senha->execute()) {
                    $_SESSION['resposta'] = "Senha alterada com sucesso!";
                } else {
                    $_SESSION['resposta'] = "Erro ao trocar a senha!";
                }
            } else {
                $_SESSION['resposta'] = "As senhas não são iguais!";
            }
        } else {
            $_SESSION['resposta'] = "Senha incorreta!";
        };
    }
} else {
    $_SESSION['resposta'] = "Método de requisição inválido.";
}

header("Location: ../../../pages/user_config.php");
exit();
$conn->close();
