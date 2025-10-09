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
        $_SESSION['resposta'] = "ID da despesa inválido!";
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    // Sanitiza todos os dados do formulário
    $descricao = trim(strip_tags($_POST['descricao']));
    $valor = trim(strip_tags($_POST['valor']));
    $status = trim(strip_tags($_POST['status']));
    $recorrente = trim(strip_tags($_POST['recorrente']));
    $categoria = trim(strip_tags($_POST['categoria']));
    $data = trim(strip_tags($_POST['data']));

    // Validar a descrição
    $descricao = validarDescricao($descricao);
    if ($descricao == false) {
        $_SESSION['resposta'] = "Descrição inválida!";
        header($redirecionamento);
        exit;
    }

    // Validar o valor
    $valor = validarValor($valor);
    if ($valor == false) {
        $_SESSION['resposta'] = "Valor inválido!";
        header($redirecionamento);
        exit;
    }

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header($redirecionamento);
        exit;
    }

    try {
        $sql = "UPDATE despesas SET descricao = ?, valor = ?, status = ?, recorrente = ?, categoria = ?, data = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssiiisii", $descricao, $valor, $status, $recorrente, $categoria, $data, $id, $usuario_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['resposta'] = "Despesa atualizada com sucesso!";
            } else {
                $_SESSION['resposta'] = "Nenhuma alteração foi feita ou você não tem permissão para editar.";
            }
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar a despesa!";
        }

        $stmt->close();
        header($redirecionamento);
        exit;

    } catch (Exception $erro) {
        error_log($erro->getMessage());
        $_SESSION['resposta'] = "Erro inesperado. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: " . BASE_URL . "despesas");
    exit;
}