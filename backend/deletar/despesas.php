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
        $msg = "ID inválido para exclusão.";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $msg = "Token de segurança inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    try {
        // Verificar se é um registro de adiantamento
        $sqlCheck = "SELECT tipo, status FROM despesas WHERE id = ? AND usuario_id = ?";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id, $usuario_id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $despesaCheck = $resCheck->fetch_assoc();
        $stmtCheck->close();

        if ($despesaCheck && ($despesaCheck['tipo'] == 1 || $despesaCheck['status'] == 2)) {
            $msg = "Esta despesa faz parte de um adiantamento. Use a opção de cancelar adiantamento.";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
            header($redirecionamento);
            exit;
        }

        // Antes de deletar, limpar referências de adiantamento (para evitar FK violation)
        // Se esta despesa é referenciada por outra via adiantamento_ref_id, limpar
        $sqlLimpaRef = "UPDATE despesas SET adiantamento_ref_id = NULL WHERE adiantamento_ref_id = ? AND usuario_id = ?";
        $stmtLR = $conexao->prepare($sqlLimpaRef);
        $stmtLR->bind_param("ii", $id, $usuario_id);
        $stmtLR->execute();
        $stmtLR->close();

        $sql = "DELETE FROM despesas WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ii", $id, $usuario_id);

        if ($stmt->execute()) {
            limparInsightsCache();
            if ($stmt->affected_rows > 0) {
                $msg = "Despesa excluída com sucesso!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            } else {
                $msg = "Não foi possível excluir a despesa. Verifique as permissões.";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(false, $msg);
            }
        } else {
            $msg = "Ocorreu um erro ao tentar excluir a despesa.";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
        }

        $stmt->close();
        header($redirecionamento);
        exit;

    } catch (Exception $erro) {
        error_log($erro->getMessage());
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
    header("Location: " . BASE_URL . "despesas");
    exit;
}