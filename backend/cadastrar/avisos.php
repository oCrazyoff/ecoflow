<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];

    // Strings (removendo espaços e caracteres perigosos)
    $titulo = trim(strip_tags($_POST['titulo']));
    $conteudo = trim(strip_tags($_POST['conteudo']));

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "avisos?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "avisos";
    }

    // validar o titulo
    $titulo = validarDescricao($titulo);
    if ($titulo == false) {
        $msg = "Titulo inválido!";
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
        $sql = "INSERT INTO avisos (titulo, conteudo) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $titulo, $conteudo);

        if ($stmt->execute()) {
            $msg = "Aviso cadastrado com sucesso!";
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

header("Location: " . BASE_URL . "avisos");
$stmt = null;
exit;