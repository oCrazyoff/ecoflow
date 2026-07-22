<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];

    // Strings (removendo espaços e caracteres perigosos)
    $descricao = trim(strip_tags($_POST['descricao']));
    $valor = trim(strip_tags($_POST['valor']));
    $recorrente = trim(strip_tags($_POST['recorrente']));
    $data = trim(strip_tags($_POST['data']));

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "rendas?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "rendas";
    }

    // validar a descrição
    $descricao = validarDescricao($descricao);
    if ($descricao == false) {
        $msg = "Descrição inválida!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    // validar o valor
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
        // Gerar UUID de recorrência se for recorrente
        $recorrencia_grupo = null;
        if ($recorrente == 1) {
            $recorrencia_grupo = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
        }

        $sql = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, recorrencia_grupo, data) VALUES (?,?,?,?,?,?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ississ", $usuario_id, $descricao, $valor, $recorrente, $recorrencia_grupo, $data);

        if ($stmt->execute()) {
            limparInsightsCache();
            $msg = "Renda cadastrada com sucesso!";
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

header("Location: " . BASE_URL . "rendas");
$stmt = null;
exit;