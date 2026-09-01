<?php
if (!isset($_SESSION["id"]) && isset($_COOKIE["ecoflow_lembrar"])) {
    require_once __DIR__ . "/validacoes_login.php";
    $token = $_COOKIE["ecoflow_lembrar"];
    $token_hash = hash("sha256", $token);

    $stmt_lembrar = $conexao->prepare("SELECT id, nome, email, cargo, ultima_verificacao FROM usuarios WHERE lembrar_token = ?");
    $stmt_lembrar->bind_param("s", $token_hash);
    
    if ($stmt_lembrar->execute()) {
        $stmt_lembrar->bind_result($id_lembrar, $nome_lembrar, $email_lembrar, $cargo_lembrar, $ultima_verificacao_db);
        if ($stmt_lembrar->fetch()) {
            $_SESSION["id"] = $id_lembrar;
            $_SESSION["nome"] = $nome_lembrar;
            $_SESSION["email"] = $email_lembrar;
            $_SESSION["cargo"] = $cargo_lembrar;
            
            // Necessario fechar o stmt antes de chamar a funcao que usa novas consultas
            $stmt_lembrar->close();
            $stmt_lembrar_closed = true;

            processarVerificacoesLogin($id_lembrar, $cargo_lembrar, $ultima_verificacao_db);
        }
    }
    
    if (!isset($stmt_lembrar_closed)) {
        $stmt_lembrar->close();
    }
}

