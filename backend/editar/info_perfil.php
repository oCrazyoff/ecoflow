<?php
require_once __DIR__ . '/../valida.php';
require_once __DIR__ . '/../auth/funcoes_auth.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Define o redirecionamento padrão para a página de perfil
    $redirecionamento = "Location: " . BASE_URL . "perfil";

    // Pega os dados do usuário logado e do formulário
    $usuario_id = $_SESSION['id'];
    $nome_atual = $_SESSION['nome'];
    $email_atual = $_SESSION['email'];

    $novo_nome = trim(strip_tags($_POST['nome'] ?? ''));
    $novo_email = trim(strip_tags($_POST['email'] ?? ''));

    // verificar se os dados enviados não são os mesmos que já estão salvos
    if ($novo_nome === $nome_atual && $novo_email === $email_atual) {
        $_SESSION['resposta'] = "Nenhuma alteração foi feita.";
        header($redirecionamento);
        exit;
    }

    // validar o nome
    $novo_nome = validarNome($novo_nome);

    // validar o email
    if (validarEmail($novo_email) == false) {
        $_SESSION['resposta'] = "O e-mail fornecido é inválido!";
        header($redirecionamento);
        exit;
    }

    // verificar token CSRF
    if (validarCSRF($_POST['csrf']) == false) {
        $_SESSION['resposta'] = "Token de segurança inválido. Tente novamente.";
        header($redirecionamento);
        exit;
    }

    try {
        if ($novo_email !== $email_atual) {
            $sql_check = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
            $stmt_check = $conexao->prepare($sql_check);
            $stmt_check->bind_param("si", $novo_email, $usuario_id);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $_SESSION['resposta'] = "Este e-mail já está em uso por outra conta.";
                $stmt_check->close();
                header($redirecionamento);
                exit;
            }
            $stmt_check->close();
        }

        // se passou em todas as validações, atualiza as informações
        $sql_update = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
        $stmt_update = $conexao->prepare($sql_update);
        $stmt_update->bind_param("ssi", $novo_nome, $novo_email, $usuario_id);

        if ($stmt_update->execute()) {
            // Se a atualização teve sucesso, atualiza também a sessão!
            $_SESSION['nome'] = $novo_nome;
            $_SESSION['email'] = $novo_email;
            $_SESSION['resposta'] = "Informações atualizadas com sucesso!";
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar as informações.";
        }

        $stmt_update->close();
        header($redirecionamento);
        exit;

    } catch (Exception $erro) {
        $_SESSION['resposta'] = "Erro inesperado no servidor. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação inválido!";
    header("Location: " . BASE_URL . "perfil");
    exit;
}