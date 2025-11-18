<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];

    // Strings (removendo espaços e caracteres perigosos)
    $titulo = trim(strip_tags($_POST['titulo']));
    $conteudo = trim(strip_tags($_POST['conteudo']));

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "avisos?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "avisos";
    }

    // validar o titulo
    $titulo = validarDescricao($titulo);
    if ($titulo == false) {
        $_SESSION['resposta'] = "Titulo inválido!";
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
        $sql = "INSERT INTO avisos (titulo, conteudo) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("ss", $titulo, $conteudo);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Aviso cadastrado com sucesso!";
            header($redirecionamento);
            $stmt->close();
            exit;
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro!";
            header($redirecionamento);
            $stmt->close();
            exit;
        }
    } catch (Exception $erro) {
        // Caso houver erro ele retorna
        switch ($erro->getCode()) {
            default:
                $_SESSION['resposta'] = "Erro inesperado. Tente novamente.";
                header($redirecionamento);
                exit;
        }
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
}

header("Location: " . BASE_URL . "avisos");
$stmt = null;
exit;