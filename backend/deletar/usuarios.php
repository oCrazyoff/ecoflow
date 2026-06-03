<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "usuarios?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "usuarios";
    }

    if (!$id) {
        $msg = "ID inválido para exclusão.";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    // validar csrf
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $msg = "Token de segurança inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    try {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $msg = "Usuário excluído com sucesso!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            } else {
                $msg = "Não foi possível excluir o usuário. Verifique as permissões.";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(false, $msg);
            }
        } else {
            $msg = "Ocorreu um erro ao tentar excluir o usuário.";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
        }

        $stmt->close();
        header($redirecionamento);
        exit;
    } catch (Exception $erro) {
        $msg = "Erro inesperado no servidor. Tente novamente.";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }
} else {
    // Redireciona se o método não for POST
    $msg = "Método de solicitação inválido.";
    $_SESSION['resposta'] = $msg;
    if (isAjax()) responderJSON(false, $msg);
    header("Location: " . BASE_URL . "usuarios");
    exit;
}
