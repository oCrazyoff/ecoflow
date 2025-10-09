<?php
session_start();
session_unset();
session_destroy();

// Invalida o cookie de sessão no navegador do usuário, forçando-o a "esquecer" a sessão antiga.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_start();
$_SESSION['resposta'] = "Você foi desconectado com sucesso!";

require_once "backend/conexao.php";
header("Location: " . BASE_URL . "login");
exit;