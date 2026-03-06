<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];

    // Strings (removendo espaços e caracteres perigosos)
    $nome = trim(strip_tags($_POST['nome']));

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "categorias?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "categorias";
    }

    // validar o nome
    $nome = validarDescricao($nome);
    if ($nome == false) {
        $_SESSION['resposta'] = "Nome inválido!";
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
        $sql = "INSERT INTO categorias (usuario_id, nome) VALUES (?, ?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $nome);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Categoria cadastrada com sucesso!";
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

header("Location: " . BASE_URL . "categorias");
$stmt = null;
exit;
