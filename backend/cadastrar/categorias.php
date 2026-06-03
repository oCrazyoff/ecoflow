<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];

    // Strings (removendo espaços e caracteres perigosos)
    $nome = trim(strip_tags($_POST['nome']));

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "categorias?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "categorias";
    }

    // validar o nome
    $nome = validarDescricao($nome);
    if ($nome == false) {
        $msg = "Nome inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $msg = "Token Inválido";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    try {
        $sql = "INSERT INTO categorias (usuario_id, nome) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $nome);

        if ($stmt->execute()) {
            $msg = "Categoria cadastrada com sucesso!";
            $_SESSION['resposta'] = $msg;
            $stmt->close();
            if (isAjax()) responderJSON(true, $msg);
            header($redirecionamento);
            exit;
        } else {
            $msg = "Ocorreu um erro!";
            $_SESSION['resposta'] = $msg;
            $stmt->close();
            if (isAjax()) responderJSON(false, $msg);
            header($redirecionamento);
            exit;
        }
    } catch (Exception $erro) {
        // Caso houver erro ele retorna
        switch ($erro->getCode()) {
            default:
                $msg = "Erro inesperado. Tente novamente.";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(false, $msg);
                header($redirecionamento);
                exit;
        }
    }
} else {
    $msg = "Método de solicitação ínvalido!";
    $_SESSION['resposta'] = $msg;
    if (isAjax()) responderJSON(false, $msg);
}

header("Location: " . BASE_URL . "categorias");
$stmt = null;
exit;
