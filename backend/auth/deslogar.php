<?php
session_start();
$userId = $_SESSION['id'] ?? null;

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

require_once __DIR__ . "/../conexao.php";

// Limpa o cookie e o token de lembrar-me
if (isset($_COOKIE['ecoflow_lembrar'])) {
    if ($userId) {
        $stmtClear = $conexao->prepare("UPDATE usuarios SET lembrar_token = NULL WHERE id = ?");
        $stmtClear->bind_param("i", $userId);
        $stmtClear->execute();
        $stmtClear->close();
    }
    setcookie('ecoflow_lembrar', '', time() - 3600, "/");
}

session_start();
$_SESSION['resposta'] = "Você foi desconectado com sucesso!";

require_once "backend/conexao.php";
header("Location: " . BASE_URL . "login");
exit;