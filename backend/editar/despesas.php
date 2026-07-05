<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "despesas?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "despesas";
    }

    if (!$id) {
        $msg = "ID da despesa inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    // Sanitiza todos os dados do formulário
    $descricao = trim(strip_tags($_POST['descricao']));
    $valor = trim(strip_tags($_POST['valor']));
    $status = trim(strip_tags($_POST['status']));
    $recorrente = trim(strip_tags($_POST['recorrente']));
    $categoria = trim(strip_tags($_POST['categoria_id']));
    $data = trim(strip_tags($_POST['data']));

    // Campos de parcelas
    $editar_todas = isset($_POST['editar_todas']) ? intval($_POST['editar_todas']) : 0;
    $parcela_grupo = isset($_POST['parcela_grupo']) ? trim(strip_tags($_POST['parcela_grupo'])) : '';

    // Validar a descrição
    $descricao = validarDescricao($descricao);
    if ($descricao == false) {
        $msg = "Descrição inválida!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // Validar o valor
    $valor = validarValor($valor);
    if ($valor === false) {
        $msg = "Valor inválido!";
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
        // Verifica se deve editar todas as parcelas do grupo
        if ($editar_todas == 1 && !empty($parcela_grupo)) {
            // Atualiza todas as despesas do mesmo grupo de parcelas
            // Mantém a descrição base (remove o sufixo de parcela existente e re-aplica)
            // Atualiza valor, status, recorrente e categoria em todas
            $sql = "UPDATE despesas SET valor = ?, status = ?, recorrente = ?, categoria_id = ? WHERE parcela_grupo = ? AND usuario_id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("siiisi", $valor, $status, $recorrente, $categoria, $parcela_grupo, $usuario_id);

            if ($stmt->execute()) {
                limparInsightsCache();
                if ($stmt->affected_rows > 0) {
                    $msg = "Todas as parcelas foram atualizadas com sucesso!";
                    $_SESSION['resposta'] = $msg;
                    if (isAjax()) responderJSON(true, $msg);
                } else {
                    $msg = "Nenhuma alteração foi feita ou você não tem permissão para editar.";
                    $_SESSION['resposta'] = $msg;
                    if (isAjax()) responderJSON(true, $msg);
                }
            } else {
                $msg = "Ocorreu um erro ao atualizar as parcelas!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(false, $msg);
            }
        } else {
            // Edição individual (padrão)
            $sql = "UPDATE despesas SET descricao = ?, valor = ?, status = ?, recorrente = ?, categoria_id = ?, data = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("ssiiisii", $descricao, $valor, $status, $recorrente, $categoria, $data, $id, $usuario_id);

            if ($stmt->execute()) {
                limparInsightsCache();
                if ($stmt->affected_rows > 0) {
                    $msg = "Despesa atualizada com sucesso!";
                    $_SESSION['resposta'] = $msg;
                    if (isAjax()) responderJSON(true, $msg);
                } else {
                    $msg = "Nenhuma alteração foi feita ou você não tem permissão para editar.";
                    $_SESSION['resposta'] = $msg;
                    if (isAjax()) responderJSON(true, $msg);
                }
            } else {
                $msg = "Ocorreu um erro ao atualizar a despesa!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(false, $msg);
            }
        }

        $stmt->close();
        header($redirecionamento);
        exit;
    } catch (Exception $erro) {
        error_log($erro->getMessage());
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
    header("Location: " . BASE_URL . "despesas");
    exit;
}
