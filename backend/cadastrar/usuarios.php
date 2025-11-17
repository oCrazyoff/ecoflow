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

    $nome = trim(strip_tags($_POST['nome']));
    $email = trim(strip_tags($_POST['email']));
    $senha = trim($_POST["senha"]);
    $cargo = trim(strip_tags($_POST['cargo']));
    $csrf = trim(strip_tags($_POST["csrf"]));

    // Verificar token CSRF
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token de segurança inválido!";
        header($redirecionamento);
        exit;
    }

    // Verificar se algum campo está vazio (cargo não pode estar vazio)
    if (empty($nome) || empty($email) || empty($senha) || $cargo === '') {
        $_SESSION['resposta'] = "Por favor, preencha todos os campos!";
        header($redirecionamento);
        exit;
    }

    // validar o nome
    $nome = validarNome($nome);

    // Validar o e-mail
    if (validarEmail($email) == false) {
        $_SESSION['resposta'] = "Formato de e-mail inválido!";
        header($redirecionamento);
        exit;
    }

    // validar senha
    if (validarSenha($senha) == false) {
        $_SESSION['resposta'] = "Senha inválida!";
        header($redirecionamento);
        exit;
    }

    // validando cargo
    if ($cargo !== '0' && $cargo !== '1') {
        $_SESSION['resposta'] = "Cargo inválido! (Deve ser 0 ou 1)";
        header($redirecionamento);
        exit;
    }
    $cargo_int = (int)$cargo; // Converte para inteiro


    try {
        // verificar se o e-mail já existe
        $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['resposta'] = "Este e-mail já está cadastrado!";
            $stmt->close();
            header($redirecionamento);
            exit;
        }
        $stmt->close();

        // Criptografar a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        // query
        $stmtInsert = $conexao->prepare("INSERT INTO usuarios (nome, email, senha_hash, cargo) VALUES (?, ?, ?, ?)");
        $stmtInsert->bind_param("sssi", $nome, $email, $senha_hash, $cargo_int);

        if ($stmtInsert->execute()) {
            $_SESSION['resposta'] = "Usuário cadastrado com sucesso!";
            header($redirecionamento);
            exit;
        } else {
            throw new Exception("Não foi possível executar o cadastro.");
        }
    } catch (Exception $erro) {
        $_SESSION['resposta'] = "Ocorreu um erro inesperado. Tente novamente mais tarde.";
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
