<?php
require_once "conexao.php";

// Verifica se existe uma sessão ativa e se não houver inicia uma
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$rota = $rota ?? false;

// 1. PRIMEIRO: Verificamos a autenticação e ATUALIZAMOS os dados do banco
if (!isset($_SESSION["id"]) && !isset($_SESSION["nome"]) && !isset($_SESSION["email"])) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "login");
    exit();
} else {
    $id = $_SESSION["id"];
    // ADICIONADO: relatorio_anual_pendente na consulta
    $stmt = $conexao->prepare("SELECT nome, email, cargo, relatorio_anual_pendente FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // ADICIONADO: variável para receber o status do relatório
        $stmt->bind_result($nome, $email, $cargo, $relatorio_db);
        $stmt->fetch();
        $stmt->close();

        if (($nome === null) || ($email === null) || ($cargo === null)) {
            session_unset();
            session_destroy();
            header("Location: " . BASE_URL . "login");
            exit();
        } else {
            // Atualiza a sessão com os dados frescos do banco
            $_SESSION["nome"] = $nome;
            $_SESSION["email"] = $email;
            $_SESSION["cargo"] = $cargo;
            // CORREÇÃO CRUCIAL: Atualiza o status do relatório na sessão
            $_SESSION['relatorio_pendente'] = (bool)$relatorio_db;
        }
    } else {
        $_SESSION['resposta'] = "Erro inesperado!";
        header("Location: " . BASE_URL . "login");
        exit();
    }
}

// 2. SEGUNDO: Agora fazemos as validações de rota com a sessão atualizada

// Pega o status atualizado
$pendente = $_SESSION['relatorio_pendente'] ?? false;

// Bloqueia navegação se tiver relatório pendente (exceto na tela de relatório)
if ($pendente && $rota !== 'relatorio' && $rota !== 'finalizar_relatorio') {
    $_SESSION['resposta'] = "Gere um relatório primeiro!";
    header("Location: " . BASE_URL . "relatorio");
    exit();
}

// Bloqueia acesso à tela de relatório se NÃO tiver pendência
if ($pendente == false && $rota === 'relatorio') {
    header("Location: " . BASE_URL . "dashboard");
    exit();
}

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
        header("Location:" . BASE_URL . "dashboard");
        exit();
    }
}
