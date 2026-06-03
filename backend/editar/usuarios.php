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
        $msg = "Token de segurança inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // Validar o ID (que veio do GET)
    if (!$id) {
        $msg = "ID de usuário inválido para edição.";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // Validar campos obrigatórios (Nome, Email e Cargo)
    if (empty($nome) || empty($email) || $cargo === '') {
        $msg = "Nome, e-mail e cargo são obrigatórios!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // validar nome
    $nome = validarNome($nome);

    // validar email
    if (validarEmail($email) == false) {
        $msg = "Formato de e-mail inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // Validar a SENHA (Apenas se ela foi informada)
    if (!empty($senha)) {
        if (validarSenha($senha) == false) {
            $msg = "Senha inválida! Se informada, deve seguir os critérios.";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
            header($redirecionamento);
            exit;
        }
    }

    // validando o carog
    if ($cargo !== '0' && $cargo !== '1') {
        $msg = "Cargo inválido! (Deve ser 0 ou 1)";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
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
            $msg = "Este e-mail já está em uso por outro usuário!";
            $_SESSION['resposta'] = $msg;
            $stmt->close();
            if (isAjax()) responderJSON(false, $msg);
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
                $msg = "Usuário atualizado com sucesso!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            } else {
                $msg = "Nenhum dado foi alterado.";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            }
        } else {
            throw new Exception("Não foi possível executar a atualização.");
        }

        $stmtUpdate->close();
        header($redirecionamento);
        exit;
    } catch (Exception $erro) {
        $msg = "Ocorreu um erro inesperado. Tente novamente.";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
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

    $msg = "Método de requisição inválido!";
    $_SESSION['resposta'] = $msg;
    if (isAjax()) responderJSON(false, $msg);
    header($redirecionamento);
    exit;
}
