<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $inputJSON = file_get_contents('php://input');
    $inputData = json_decode($inputJSON, true);

    $id = $inputData['id'] ?? filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $modo = $inputData['modo'] ?? ($_POST['modo'] ?? null);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "rendas?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "rendas";
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
    $csrf = trim(strip_tags($inputData['csrf'] ?? $_POST["csrf"] ?? ''));
    if (validarCSRF($csrf) == false) {
        $msg = "Token de segurança inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    try {
        // Verificar se a renda é recorrente
        $sqlCheck = "SELECT recorrente FROM rendas WHERE id = ? AND usuario_id = ?";
        $stmtCheck = $conexao->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id, $usuario_id);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        $renda = $resCheck->fetch_assoc();
        $stmtCheck->close();

        if ($renda && $renda['recorrente'] == 1) {
            $sqlDel = "UPDATE rendas SET ignorado = 1 WHERE id = ? AND usuario_id = ?";
            $stmtD = $conexao->prepare($sqlDel);
            $stmtD->bind_param("ii", $id, $usuario_id);
            $sucesso = $stmtD->execute();
            $stmtD->close();
        } else {
            // Fluxo normal para não recorrentes
            $sql = "DELETE FROM rendas WHERE id = ? AND usuario_id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ii", $id, $usuario_id);
            $sucesso = $stmt->execute();
            $stmt->close();
        }

        if ($sucesso) {
            limparInsightsCache();
            $msg = "Renda excluída com sucesso!";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(true, $msg);
        } else {
            $msg = "Não foi possível excluir a renda.";
            $_SESSION['resposta'] = $msg;
            if (isAjax()) responderJSON(false, $msg);
        }

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
    header("Location: " . BASE_URL . "rendas");
    exit;
}