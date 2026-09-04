<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputJSON = file_get_contents('php://input');
    $inputData = json_decode($inputJSON, true);

    $id = $inputData['id'] ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $modo = $inputData['modo'] ?? ($_POST['modo'] ?? null);

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

    $csrf = trim(strip_tags($inputData['csrf'] ?? $_POST["csrf"] ?? ''));
    if (validarCSRF($csrf) == false) {
        $msg = "Token de segurança inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    try {
        // Verificar se a despesa é recorrente
        $sqlCheck = "SELECT recorrente FROM despesas WHERE id = ? AND usuario_id = ?";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id, $usuario_id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $despesa = $resCheck->fetch_assoc();
        $stmtCheck->close();

        if ($despesa && $despesa['recorrente'] == 1) {
            $sqlDel = "UPDATE despesas SET ignorado = 1 WHERE id = ? AND usuario_id = ?";
            $stmtD = $conexao->prepare($sqlDel);
            $stmtD->bind_param("ii", $id, $usuario_id);
            $sucesso = $stmtD->execute();
            $stmtD->close();
        } else {
            // Fluxo normal para não recorrentes
            // Antes de deletar, limpar referências de adiantamento (para evitar FK violation)
            $sqlLimpaRef = "UPDATE despesas SET adiantamento_ref_id = NULL WHERE adiantamento_ref_id = ? AND usuario_id = ?";
            $stmtLR = $conexao->prepare($sqlLimpaRef);
            $stmtLR->bind_param("ii", $id, $usuario_id);
            $stmtLR->execute();
            $stmtLR->close();

            $sql = "DELETE FROM despesas WHERE id = ? AND usuario_id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ii", $id, $usuario_id);
            $sucesso = $stmt->execute();
            $stmt->close();
        }

        if ($sucesso) {
            limparInsightsCache();
            $msg = "Despesa excluída com sucesso!";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(true, $msg);
        } else {
            $msg = "Não foi possível excluir a despesa.";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
        }

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