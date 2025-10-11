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
        $_SESSION['resposta'] = "ID da renda inválido!";
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
        $_SESSION['resposta'] = "Descrição inválida!";
        header($redirecionamento);
        exit;
    }

    // Validar o valor
    $valor = validarValor($valor);
    if ($valor === false) {
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
        $sql = "UPDATE rendas SET descricao = ?, valor = ?, recorrente = ?, data = ? WHERE id = ? AND usuario_id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssisii", $descricao, $valor, $recorrente, $data, $id, $usuario_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['resposta'] = "Renda atualizada com sucesso!";
            } else {
                $_SESSION['resposta'] = "Nenhuma alteração foi feita.";
            }
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar a renda!";
        }

        $stmt->close();
        header($redirecionamento);
        exit;

    } catch (Exception $erro) {
        $_SESSION['resposta'] = "Erro inesperado. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
    header("Location: " . BASE_URL . "rendas");
    exit;
}