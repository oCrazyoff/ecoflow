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
        $msg = "Token de segurança inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // Verificar se algum campo está vazio (cargo não pode estar vazio)
    if (empty($nome) || empty($email) || empty($senha) || $cargo === '') {
        $msg = "Por favor, preencha todos os campos!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // validar o nome
    $nome = validarNome($nome);

    // Validar o e-mail
    if (validarEmail($email) == false) {
        $msg = "Formato de e-mail inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // validar senha
    if (validarSenha($senha) == false) {
        $msg = "Senha inválida!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // validando cargo
    if ($cargo !== '0' && $cargo !== '1') {
        $msg = "Cargo inválido! (Deve ser 0 ou 1)";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
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
            $msg = "Este e-mail já está cadastrado!";
            $_SESSION['resposta'] = $msg;
            $stmt->close();
            if (isAjax()) responderJSON(false, $msg);
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
            $novo_usuario_id = $conexao->insert_id;
            $categorias_padrao = ['Casa', 'Alimentação', 'Transporte', 'Saúde', 'Educação', 'Lazer', 'Cartão', 'Outro'];

            $stmt_cat = $conexao->prepare("INSERT INTO categorias (usuario_id, nome) VALUES (?, ?)");
            foreach ($categorias_padrao as $nome_cat) {
                $stmt_cat->bind_param("is", $novo_usuario_id, $nome_cat);
                $stmt_cat->execute();
            }
            $stmt_cat->close();
            // ----------------------------------------

            $msg = "Usuário cadastrado com sucesso!";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(true, $msg);
            header($redirecionamento);
            exit;
        } else {
            throw new Exception("Não foi possível executar o cadastro.");
        }
    } catch (Exception $erro) {
        $msg = "Ocorreu um erro inesperado. Tente novamente mais tarde.";
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
