<?php
session_start();
require_once("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (isset($_POST['_csrf']) && $_POST['_csrf'] !== $_SESSION['_csrf']) {
        $_SESSION['resposta'] = 'Erro de CSRF';
        $_SESSION['_csrf'] = hash('sha256', random_bytes(32));
        header('Location: ../cadastro.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['resposta'] = 'E-mail inválido';
        header('Location: ../cadastro.php');
        exit();
    }

    if (strlen($senha) < 8) {
        $_SESSION['resposta'] = 'A senha deve ter pelo menos 8 caracteres';
        header('Location: ../cadastro.php');
        exit();
    }

    // Verificando se o email já existe
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['resposta'] = 'Este e-mail já está cadastrado';
        header('Location: ../../cadastro.php');
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome, $email, $senha_hash);

    if ($stmt->execute()) {
        $_SESSION['resposta'] = 'Cadastro realizado com sucesso! Você pode fazer login agora.';
        header('Location: ../../login.php');
        exit();
    } else {
        $_SESSION['resposta'] = 'Erro ao realizar o cadastro. Tente novamente.';
        header('Location: ../../cadastro.php');
        exit();
    }
} else {
    $_SESSION['resposta'] = 'Método não permitido';
}

header('Location: ../../cadastro.php');
exit();
