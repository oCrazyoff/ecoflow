<?php
require_once __DIR__ . '/../valida.php';
require_once __DIR__ . '/../auth/funcoes_auth.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

// Define o redirecionamento padrão para a página de perfil do usuário
    $redirecionamento = "Location: " . BASE_URL . "perfil";

    $usuario_id = $_SESSION['id'];

// Pega os dados do formulário
    $senhaAtual = trim($_POST['senha-atual'] ?? '');
    $novaSenha = trim($_POST['nova-senha'] ?? '');
    $confirmarSenha = trim($_POST['confirmar-senha'] ?? '');


// verificar se algum campo está vazio
    if (empty($senhaAtual) || empty($novaSenha) || empty($confirmarSenha)) {
        $_SESSION['resposta'] = "Todos os campos de senha são obrigatórios!";
        header($redirecionamento);
        exit;
    }

// verificar se a nova senha e a confirmação são iguais
    if ($novaSenha !== $confirmarSenha) {
        $_SESSION['resposta'] = "A nova senha e a confirmação não conferem!";
        header($redirecionamento);
        exit;
    }

// verificar se a nova senha é diferente da senha atual
    if ($novaSenha === $senhaAtual) {
        $_SESSION['resposta'] = "A nova senha não pode ser igual à senha atual!";
        header($redirecionamento);
        exit;
    }

// validar a força da nova senha
    if (validarSenha($novaSenha) == false) {
        $_SESSION['resposta'] = "A nova senha é inválida!";
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
        $sql_select = "SELECT senha_hash FROM usuarios WHERE id = ?";
        $stmt_select = $conexao->prepare($sql_select);
        $stmt_select->bind_param("i", $usuario_id);
        $stmt_select->execute();
        $stmt_select->bind_result($senha_hash_db);
        $stmt_select->fetch();
        $stmt_select->close();

        // se não encontrou um hash para o usuário.
        if (!$senha_hash_db) {
            $_SESSION['resposta'] = "Erro ao encontrar dados do usuário.";
            header($redirecionamento);
            exit;
        }

        // verificar se a "senha atual" fornecida corresponde ao hash do banco
        if (password_verify($senhaAtual, $senha_hash_db)) {

            // criar um novo hash seguro para a nova senha
            $nova_senha_hash = password_hash($novaSenha, PASSWORD_DEFAULT);

            // atualizar a senha no banco de dados
            $sql_update = "UPDATE usuarios SET senha_hash = ? WHERE id = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("si", $nova_senha_hash, $usuario_id);

            if ($stmt_update->execute()) {
                $_SESSION['resposta'] = "Senha atualizada com sucesso!";
            } else {
                $_SESSION['resposta'] = "Ocorreu um erro ao atualizar a senha.";
            }
            $stmt_update->close();

        } else {
            $_SESSION['resposta'] = "A senha atual está incorreta!";
        }

        header($redirecionamento);
        exit;

    } catch (Exception $erro) {
        // Em caso de erro inesperado no banco de dados
        $_SESSION['resposta'] = "Erro inesperado no servidor. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação inválido!";
    header("Location: " . BASE_URL . "perfil");
    exit;
}