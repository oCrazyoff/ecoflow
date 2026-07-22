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
    $categoria = trim(strip_tags($_POST['categoria_id']));

    // Campos de parcelas
    $parcelado = isset($_POST['parcelado']) ? intval($_POST['parcelado']) : 0;
    $num_parcelas = isset($_POST['num_parcelas']) ? intval($_POST['num_parcelas']) : 0;

    // lógica de redirecionamento
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "despesas?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "despesas";
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
        // Verifica se é parcelado
        if ($parcelado == 1 && $num_parcelas >= 2) {
            // Gera UUID para o grupo de parcelas
            $parcela_grupo = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // Calcula valor por parcela (divide igualmente)
            $valor_parcela = round($valor / $num_parcelas, 2);
            // Ajusta a última parcela para compensar arredondamento
            $valor_ultima = $valor - ($valor_parcela * ($num_parcelas - 1));

            $sql = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data, parcela_grupo, parcela_numero, parcela_total) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $stmt = $conexao->prepare($sql);

            $descricao_base = $descricao;

            for ($i = 1; $i <= $num_parcelas; $i++) {
                // Descrição com sufixo de parcela
                $descricao_parcela = $descricao_base . " (" . $i . "/" . $num_parcelas . ")";

                // Valor: última parcela pode ser diferente por arredondamento
                $valor_atual = ($i == $num_parcelas) ? $valor_ultima : $valor_parcela;

                // Data: incrementa 1 mês por parcela (a primeira usa a data original)
                if ($i == 1) {
                    $data_parcela = $data;
                } else {
                    $data_obj = new DateTime($data);
                    $data_obj->modify('+' . ($i - 1) . ' months');
                    $data_parcela = $data_obj->format('Y-m-d');
                }

                $stmt->bind_param(
                    "issiiissii",
                    $usuario_id,
                    $descricao_parcela,
                    $valor_atual,
                    $status,
                    $recorrente,
                    $categoria,
                    $data_parcela,
                    $parcela_grupo,
                    $i,
                    $num_parcelas
                );
                $stmt->execute();
            }

            limparInsightsCache();
            $msg = "Despesa parcelada em {$num_parcelas}x cadastrada com sucesso!";
            $_SESSION['resposta'] = $msg;
            $stmt->close();
            if (isAjax()) responderJSON(true, $msg);
            header($redirecionamento);
            exit;
        } else {
            // Cadastro normal (sem parcelas)
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

            // Definir data_pagamento se já está pago
            $data_pagamento = ($status == 1) ? $data : null;

            $sql = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data, recorrencia_grupo, data_pagamento) VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $conexao->prepare($sql);
            $stmt->bind_param("issiiisss", $usuario_id, $descricao, $valor, $status, $recorrente, $categoria, $data, $recorrencia_grupo, $data_pagamento);

            if ($stmt->execute()) {
                limparInsightsCache();
                $msg = "Despesa cadastrada com sucesso!";
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
        }
    } catch (Exception $erro) {
        error_log($erro->getMessage());
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

header("Location: " . BASE_URL . "despesas");
$stmt = null;
exit;
