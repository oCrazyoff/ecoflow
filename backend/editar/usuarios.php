<?php
require_once __DIR__ . "/../auth/funcoes_auth.php";
require_once __DIR__ . "/../valida.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // --- LÓGICA DE REDIRECIONAMENTO ---
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "usuarios?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "usuarios";
    }

    // ID da URL (GET)
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Dados do formulário (POST)
    $nome = trim(strip_tags($_POST['nome']));
    $email = trim(strip_tags($_POST['email']));
    $senha = trim($_POST["senha"]);
    $cargo = trim(strip_tags($_POST['cargo']));
    $csrf = trim(strip_tags($_POST["csrf"]));

    // Validar CSRF
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token de segurança inválido!";
        header($redirecionamento);
        exit;
    }

    // Validar o ID (que veio do GET)
    if (!$id) {
        $_SESSION['resposta'] = "ID de usuário inválido para edição.";
        header($redirecionamento);
        exit;
    }

    // Validar campos obrigatórios (Nome, Email e Cargo)
    if (empty($nome) || empty($email) || $cargo === '') {
        $_SESSION['resposta'] = "Nome, e-mail e cargo são obrigatórios!";
        header($redirecionamento);
        exit;
    }

    // validar nome
    $nome = validarNome($nome);

    // validar email
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Formato de e-mail inválido!";
        header($redirecionamento);
        exit;
    }

    // Validar a SENHA (Apenas se ela foi informada)
    if (!empty($senha)) {
        if (validarSenha($senha) == false) {
            $_SESSION['resposta'] = "Senha inválida! Se informada, deve seguir os critérios.";
            header($redirecionamento);
            exit;
        }
    }

    // validando o carog
    if ($cargo !== '0' && $cargo !== '1') {
        $_SESSION['resposta'] = "Cargo inválido! (Deve ser 0 ou 1)";
        header($redirecionamento);
        exit;
    }
    $cargo_int = (int)$cargo; // Converte para inteiro

    try {
        // Verificar se o e-mail já existe EM OUTRO USUÁRIO
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['resposta'] = "Este e-mail já está em uso por outro usuário!";
            $stmt->close();
            header($redirecionamento);
            exit;
        }
        $stmt->close();

        // query
        if (!empty($senha)) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nome = ?, email = ?, senha_hash = ?, cargo = ? WHERE id = ?";
            $stmtUpdate = $conexao->prepare($sql);
            $stmtUpdate->bind_param("sssii", $nome, $email, $senha_hash, $cargo_int, $id);
        } else {
            // Se a senha veio vazia, NÃO atualiza o campo senha_hash
            $sql = "UPDATE usuarios SET nome = ?, email = ?, cargo = ? WHERE id = ?";
            $stmtUpdate = $conexao->prepare($sql);
            $stmtUpdate->bind_param("ssii", $nome, $email, $cargo_int, $id);
        }

        // Executar e tratar a resposta
        if ($stmtUpdate->execute()) {
            if ($stmtUpdate->affected_rows > 0) {
                $_SESSION['resposta'] = "Usuário atualizado com sucesso!";
            } else {
                $_SESSION['resposta'] = "Nenhum dado foi alterado.";
            }
        } else {
            throw new Exception("Não foi possível executar a atualização.");
        }

        $stmtUpdate->close();
        header($redirecionamento);
        exit;
    } catch (Exception $erro) {
        $_SESSION['resposta'] = "Ocorreu um erro inesperado. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    // Se o método não for POST
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "usuarios?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "usuarios";
    }

    $_SESSION['resposta'] = "Método de requisição inválido!";
    header($redirecionamento);
    exit;
}
