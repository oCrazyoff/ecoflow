<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "avisos?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "avisos";
    }

    if (!$id) {
        $msg = "ID do aviso inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    // Sanitiza os dados do formulário
    $titulo = trim(strip_tags($_POST['titulo']));
    $conteudo = trim(strip_tags($_POST['conteudo']));

    // Validar o titulo
    $titulo = validarDescricao($titulo);
    if ($titulo == false) {
        $msg = "Titulo inválida!";
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
        $sql = "UPDATE avisos SET titulo = ?, conteudo = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $titulo, $conteudo, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $msg = "Aviso atualizado com sucesso!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            } else {
                $msg = "Nenhuma alteração foi feita.";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            }
        } else {
            $msg = "Ocorreu um erro ao atualizar o aviso!";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
        }

        $stmt->close();
        header($redirecionamento);
        exit;
    } catch (Exception $erro) {
        $msg = "Erro inesperado. Tente novamente.";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }
} else {
    $msg = "Método de solicitação ínvalido!";
    $_SESSION['resposta'] = $msg;
    if (isAjax()) responderJSON(false, $msg);
    header("Location: " . BASE_URL . "avisos");
    exit;
}