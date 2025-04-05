<?php
session_start();
require_once("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if (isset($_POST['_csrf']) && $_POST['_csrf'] !== $_SESSION['_csrf']) {
        $_SESSION['resposta'] = 'Erro de CSRF';
        $_SESSION['_csrf'] = hash('sha256', random_bytes(32));
        header('Location: ../../login.php');
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['resposta'] = 'E-mail inválido';
        header('Location: ../../login.php');
        exit();
    }

    if (isset($_POST['email']) && isset($_POST['senha'])) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            if (password_verify($senha, $row['senha'])) {
                $_SESSION['nome'] = $row['nome'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['id'] = $row['id'];

                // Criar cookie por 1 ano
                setcookie('user_id', $_SESSION['id'], time() + (86400 * 365), "/");
                header('Location: ../../pages/dashboard.php');
                exit;

                header('Location: ../../pages/dashboard.php');
                exit();
            } else {
                $_SESSION['resposta'] = 'E-mail ou senha inválidos';
                header('Location: ../../login.php');
                exit();
            }
        } else {
            $_SESSION['resposta'] = 'E-mail ou senha inválidos';
            header('Location: ../../login.php');
            exit();
        }
    } else {
        $_SESSION['resposta'] = 'Preencha todos os campos';
        header('Location: ../../login.php');
        exit();
    }
} else {
    $_SESSION['resposta'] = 'Método não permitido';
    header('Location: ../../login.php');
    exit();
}
