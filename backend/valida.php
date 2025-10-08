<?php
require_once "conexao.php";

//Verifica se existe uma sessão ativa e se não houver inicia uma
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// verificando se tem cargo e caso não for adm impedindo as rotas de adm
if (!isset($_SESSION["cargo"]) || $_SESSION["cargo"] == 0) {
    $rota = $_GET['url'] ?? ''; // rota atual

    // rotas que o usuario comum pode acessar
    if ($rota != "login" &&
        $rota != "" &&
        $rota != "dashboard" &&
        $rota != "rendas" &&
        $rota != "despesas" &&
        $rota != "perfil"
    ) {
        $_SESSION['resposta'] = "Acesso negado!";
        header("Location: " . BASE_URL);
        exit();
    }
}

if (!isset($_SESSION["id"]) && !isset($_SESSION["nome"]) && !isset($_SESSION["email"])) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL);
    exit();
} else {
    $id = $_SESSION["id"];
    $stmt = $conexao->prepare("SELECT nome, email, cargo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->bind_result($nome, $email, $cargo);
        $stmt->fetch();
        $stmt->close();

        if (($nome === null) || ($email === null) || ($cargo === null)) {
            session_unset();
            session_destroy();
            header("Location: " . BASE_URL);
            exit();
        } else {
            $_SESSION["nome"] = $nome;
            $_SESSION["email"] = $email;
            $_SESSION["cargo"] = $cargo;
        }
    } else {
        $_SESSION['resposta'] = "Erro inesperado!";
        header("Location: " . BASE_URL);
        exit();
    }
}