<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "avisos?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "avisos";
    }

    if (!$id) {
        $_SESSION['resposta'] = "ID do aviso inválido!";
        header($redirecionamento);
        exit;
    }

    $usuario_id = $_SESSION['id'];

    // Sanitiza os dados do formulário
    $titulo = trim(strip_tags($_POST['titulo']));
    $conteudo = trim(strip_tags($_POST['conteudo']));

    // Validar o titulo
    $titulo = validarDescricao($titulo);
    if ($titulo == false) {
        $_SESSION['resposta'] = "Titulo inválida!";
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
        $sql = "UPDATE avisos SET titulo = ?, conteudo = ? WHERE id = ?";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ssi", $titulo, $conteudo, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $_SESSION['resposta'] = "Aviso atualizado com sucesso!";
            } else {
                $_SESSION['resposta'] = "Nenhuma alteração foi feita.";
            }
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro ao atualizar o aviso!";
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
    header("Location: " . BASE_URL . "avisos");
    exit;
}