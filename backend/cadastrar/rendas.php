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
        $_SESSION['resposta'] = "Descrição inválida!";
        header($redirecionamento);
        exit;
    }

    // validar o valor
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
        $sql = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, data) VALUES (?,?,?,?,?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("issis", $usuario_id, $descricao, $valor, $recorrente, $data);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Renda cadastrada com sucesso!";
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

header("Location: " . BASE_URL . "rendas");
$stmt = null;
exit;