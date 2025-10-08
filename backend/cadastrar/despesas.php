<?php
require_once __DIR__ . '/../valida.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];

    // Strings (removendo espaços e caracteres perigosos)
    $descricao = trim(strip_tags($_POST['descricao']));
    $valor = trim(strip_tags($_POST['valor']));
    $recorrente = trim(strip_tags($_POST['recorrente']));
    $data = trim(strip_tags($_POST['data']));
    $status = trim(strip_tags($_POST['status']));
    $categoria = trim(strip_tags($_POST['categoria']));

    // validar a descrição
    $descricao = validarDescricao($descricao);
    if ($descricao == false) {
        $_SESSION['resposta'] = "Descrição inválida!";
        header("Location: " . BASE_URL . "despesas");
        exit;
    }

    // validar o valor
    $valor = validarValor($valor);
    if ($valor == false) {
        $_SESSION['resposta'] = "Valor inválido!";
        header("Location: " . BASE_URL . "despesas");
        exit;
    }

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header("Location: " . BASE_URL . "despesas");
        exit;
    }

    try {
        $sql = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria, data) VALUES (?,?,?,?,?,?,?)";
        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("issiiis", $usuario_id, $descricao, $valor, $status, $recorrente, $categoria, $data);

        if ($stmt->execute()) {
            $_SESSION['resposta'] = "Despesa cadastrada com sucesso!";

            header("Location: " . BASE_URL . "despesas");
            $stmt->close();
            exit;
        } else {
            $_SESSION['resposta'] = "Ocorreu um erro!";
            header("Location: " . BASE_URL . "despesas");
            $stmt->close();
            exit;
        }
    } catch (Exception $erro) {
        error_log($erro->getMessage());
        switch ($erro->getCode()) {
            default:
                $_SESSION['resposta'] = "Erro inesperado. Tente novamente.";
                header("Location: " . BASE_URL . "despesas");
                exit;
        }
    }
} else {
    $_SESSION['resposta'] = "Método de solicitação ínvalido!";
}

header("Location: " . BASE_URL . "despesas");
$stmt = null;
exit;