<?php
require_once "conexao.php";

// Verifica se existe uma sessão ativa e se não houver inicia uma
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$rota = $rota ?? false;

// Auto-login com o Lembrar-me
if (!isset($_SESSION["id"]) && isset($_COOKIE['ecoflow_lembrar'])) {
    $token = $_COOKIE['ecoflow_lembrar'];
    $token_hash = hash('sha256', $token);

    $stmt_lembrar = $conexao->prepare("SELECT id, nome, email, cargo FROM usuarios WHERE lembrar_token = ?");
    $stmt_lembrar->bind_param("s", $token_hash);
    
    if ($stmt_lembrar->execute()) {
        $stmt_lembrar->bind_result($id_lembrar, $nome_lembrar, $email_lembrar, $cargo_lembrar);
        if ($stmt_lembrar->fetch()) {
            $_SESSION["id"] = $id_lembrar;
            $_SESSION["nome"] = $nome_lembrar;
            $_SESSION["email"] = $email_lembrar;
            $_SESSION["cargo"] = $cargo_lembrar;
        }
    }
    $stmt_lembrar->close();
}

// 1. PRIMEIRO: Verificamos a autenticação e ATUALIZAMOS os dados do banco
if (!isset($_SESSION["id"]) && !isset($_SESSION["nome"]) && !isset($_SESSION["email"])) {
    session_unset();
    session_destroy();
    if (isAjax()) responderJSON(false, "Sessão expirada. Faça login novamente.");
    header("Location: " . BASE_URL . "login");
    exit();
} else {
    $id = $_SESSION["id"];
    // ADICIONADO: relatorio_anual_pendente na consulta
    $stmt = $conexao->prepare("SELECT nome, email, cargo FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $stmt->bind_result($nome, $email, $cargo);
        $stmt->fetch();
        $stmt->close();

        if (($nome === null) || ($email === null) || ($cargo === null)) {
            session_unset();
            session_destroy();
            if (isAjax()) responderJSON(false, "Sessão inválida.");
            header("Location: " . BASE_URL . "login");
            exit();
        } else {
            // Atualiza a sessão com os dados frescos do banco
            $_SESSION["nome"] = $nome;
            $_SESSION["email"] = $email;
            $_SESSION["cargo"] = $cargo;
        }
    } else {
        $_SESSION['resposta'] = "Erro inesperado!";
        if (isAjax()) responderJSON(false, "Erro inesperado!");
        header("Location: " . BASE_URL . "login");
        exit();
    }
}

// 2. SEGUNDO: Agora fazemos as validações de rota com a sessão atualizada

// Bloqueio de rotas administrativas para usuários comuns
if ($_SESSION['cargo'] == 0) {
    if (
        $rota == 'usuarios' ||
        $rota == 'avisos' ||
        $rota == 'cadastrar_usuarios' ||
        $rota == 'editar_usuarios' ||
        $rota == 'buscar_usuarios' ||
        $rota == 'deletar_usuarios'
    ) {
        $_SESSION['resposta'] = "Acesso negado!";
        if (isAjax()) responderJSON(false, "Acesso negado!");
        header("Location:" . BASE_URL . "dashboard");
        exit();
    }
}
