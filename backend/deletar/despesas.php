<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "despesas?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "despesas";
    }

    if (!$id) {
        $_SESSION['resposta'] = "ID inválido para exclusão.";
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token de segurança inválido!";
        header($redirecionamento);
        exit;
    }

    try {
        $sql = "DELETE FROM despesas WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['resposta'] = "Despesa excluída com sucesso!";
            } else {
                $_SESSION['resposta'] = "Não foi possível excluir a despesa. Verifique as permissões.";
            }
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao tentar excluir a despesa.";
        }

        $stmt->close();
        header($redirecionamento);
        exit;

    } catch (Exception $erro) {
        error_log($erro->getMessage());
        $_SESSION['resposta'] = "Erro inesperado no servidor. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    // Redireciona se o método não for POST
    $_SESSION['resposta'] = "Método de solicitação inválido.";
    header("Location: " . BASE_URL . "despesas");
    exit;
}