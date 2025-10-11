<?php
session_start();
require_once "funcoes_auth.php";
require_once "backend/conexao.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim(strip_tags($_POST['nome']));
    $email = trim(strip_tags($_POST['email']));
    $senha = trim($_POST["senha"]);
    $confirmaSenha = trim($_POST["confirma-senha"]);
    $csrf = trim(strip_tags($_POST["csrf"]));

    // Verificar token CSRF
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Método de envio inválido!";
        header("Location: " . BASE_URL . "cadastro"); // Redireciona de volta para a pág de cadastro
        exit;
    }

    // Verificar se algum campo está vazio
    if (empty($nome) || empty($email) || empty($senha) || empty($confirmaSenha)) {
        $_SESSION['resposta'] = "Por favor, preencha todos os campos!";
        header("Location: " . BASE_URL . "cadastro");
        exit;
    }

    // validar o nome
    $nome = validarNome($nome);
    if (validarNome($nome) == false) {
        $_SESSION['resposta'] = "Nome inválido!";
        header("Location: " . BASE_URL . "cadastro");
        exit;
    }

    // Validar o e-mail
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Formato de e-mail inválido!";
        header("Location: " . BASE_URL . "cadastro");
        exit;
    }

    // validar senha
    if (validarSenha($senha) == false) {
        $_SESSION['resposta'] = "Senha inválida!";
        header("Location: " . BASE_URL . "cadastro");
        exit;
    }

    // Validar se as senhas coincidem
    if ($senha !== $confirmaSenha) {
        $_SESSION['resposta'] = "As senhas não coincidem!";
        header("Location: " . BASE_URL . "cadastro");
        exit;
    }

    try {
        // verificar se o e-mail já existe
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['resposta'] = "Este e-mail já está cadastrado!";
            $stmt->close();
            header("Location: " . BASE_URL . "cadastro");
            exit;
        }
        $stmt->close();

        // Criptografar a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmtInsert = $conexao->prepare("INSERT INTO usuarios (nome, email, senha_hash, cargo) VALUES (?, ?, ?, 0)");
        $stmtInsert->bind_param("sss", $nome, $email, $senha_hash);

        if ($stmtInsert->execute()) {
            $_SESSION['resposta_sucesso'] = "Conta criada com sucesso! Você já pode fazer o login.";
            header("Location: " . BASE_URL . "login");
            exit;
        } else {
            // Se a execução falhar por algum motivo
            throw new Exception("Não foi possível executar o cadastro.");
        }

    } catch (Exception $erro) {
        $_SESSION['resposta'] = "Ocorreu um erro inesperado. Tente novamente mais tarde.";
        header("Location: " . BASE_URL . "cadastro");
        exit;
    }
} else {
    // Se o método não for POST
    $_SESSION['resposta'] = "Método de requisição inválido!";
    header("Location: " . BASE_URL . "cadastro");
    exit;
}