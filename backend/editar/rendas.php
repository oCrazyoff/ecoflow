<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "rendas?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "rendas";
    }

    if (!$id) {
        $msg = "ID da renda inválido!";
        $_SESSION['resposta'] = $msg;
        if (isAjax()) responderJSON(false, $msg);
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    // Sanitiza os dados do formulário
    $descricao = trim(strip_tags($_POST['descricao']));
    $valor = trim(strip_tags($_POST['valor']));
    $recorrente = trim(strip_tags($_POST['recorrente']));
    $data = trim(strip_tags($_POST['data']));

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
        $sql = "UPDATE rendas SET descricao = ?, valor = ?, recorrente = ?, data = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssisii", $descricao, $valor, $recorrente, $data, $id, $usuario_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $msg = "Renda atualizada com sucesso!";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            } else {
                $msg = "Nenhuma alteração foi feita.";
                $_SESSION['resposta'] = $msg;
                if (isAjax()) responderJSON(true, $msg);
            }
        } else {
            $msg = "Ocorreu um erro ao atualizar a renda!";
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
    header("Location: " . BASE_URL . "rendas");
    exit;
}