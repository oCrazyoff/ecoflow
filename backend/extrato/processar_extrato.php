<?php
require_once __DIR__ . '/../valida.php';
require_once __DIR__ . '/../../api/deepseek.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION['id'];
    $ano_atual = date('Y');

    // lógica de redirecionamento para a dashboard
    if (isset($_SESSION['m'])) {
        $redirecionamento = "Location: " . BASE_URL . "dashboard?m=" . $_SESSION['m'];
    } else {
        $redirecionamento = "Location: " . BASE_URL . "dashboard";
    }

    // Verificar token CSRF
    $csrf = trim(strip_tags($_POST["csrf"]));
    if (validarCSRF($csrf) == false) {
        $_SESSION['resposta'] = "Token Inválido";
        header($redirecionamento);
        exit;
    }

    // Pega o extrato
    $texto_extrato = trim($_POST['conteudo_extrato']);

    if (empty($texto_extrato)) {
        $_SESSION['resposta'] = "O extrato enviado está vazio!";
        header($redirecionamento);
        exit;
    }

    try {
        // =========================================================================
        // FILTRAR E ESTRUTURAR
        // =========================================================================
        $resposta_ia = analisarExtrato($texto_extrato);

        if (empty($resposta_ia)) {
            $_SESSION['resposta'] = "A Inteligência Artificial não respondeu. Tente novamente.";
            header($redirecionamento);
            exit;
        }

        // Decodifica a resposta da IA diretamente para array
        $inicio = strpos($resposta_ia, '[');
        $fim = strrpos($resposta_ia, ']');

        if ($inicio !== false && $fim !== false) {
            $json_limpo = substr($resposta_ia, $inicio, $fim - $inicio + 1);
            $transacoes = json_decode($json_limpo, true);
        } else {
            // Se não encontrou colchetes, tenta decodificar a string inteira como último recurso
            $transacoes = json_decode($resposta_ia, true);
        }

        $transacoes = json_decode($resposta_ia, true);

        if (!is_array($transacoes)) {
            $_SESSION['resposta'] = "Erro ao processar as informações do extrato. Formato inválido.";
            header($redirecionamento);
            exit;
        }

        // =========================================================================
        // Validação de Duplicidade, Ano e Inserção
        // =========================================================================

        // queries fora do loop para manter a performance alta
        $stmt_check_renda = $conexao->prepare("SELECT id FROM rendas WHERE usuario_id = ? AND data = ? AND valor = ?");
        $stmt_check_despesa = $conexao->prepare("SELECT id FROM despesas WHERE usuario_id = ? AND data = ? AND valor = ?");

        $stmt_insert_renda = $conexao->prepare("INSERT INTO rendas (usuario_id, descricao, valor, recorrente, data) VALUES (?, ?, ?, 0, ?)");
        $stmt_insert_despesa = $conexao->prepare("INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data) VALUES (?, ?, ?, 1, 0, ?, ?)");

        $cadastrados = 0;
        $ignorados = 0;
        $id_categoria_padrao = NULL; // Fallback caso a IA não retorne uma categoria válida

        foreach ($transacoes as $transacao) {
            // Ignora a iteração se faltarem os dados vitais no array
            if (!isset($transacao['data'], $transacao['valor'], $transacao['descricao'], $transacao['tipo'])) {
                continue;
            }

            // Sanitização extra para segurança ao inserir no banco
            $data = trim(strip_tags($transacao['data']));
            $valor = (float) $transacao['valor'];
            $descricao = substr(trim(strip_tags($transacao['descricao'])), 0, 255);
            $tipo = strtolower(trim(strip_tags($transacao['tipo'])));
            $categoria_id = isset($transacao['categoria_id']) && (int)$transacao['categoria_id'] > 0 ? (int)$transacao['categoria_id'] : $id_categoria_padrao;

            // --- REGRA A: Ignorar informações que não sejam do ano atual ---
            $ano_transacao = date('Y', strtotime($data));
            if ($ano_transacao != $ano_atual) {
                $ignorados++;
                continue;
            }

            // --- REGRA B: Verificar Duplicidade e Cadastrar ---
            if ($tipo === 'renda') {
                // Checa duplicidade
                $stmt_check_renda->bind_param("isd", $usuario_id, $data, $valor);
                $stmt_check_renda->execute();

                if ($stmt_check_renda->get_result()->num_rows > 0) {
                    $ignorados++; // Já existe
                } else {
                    // Insere
                    $stmt_insert_renda->bind_param("isds", $usuario_id, $descricao, $valor, $data);
                    if ($stmt_insert_renda->execute()) $cadastrados++;
                }
            } elseif ($tipo === 'despesa') {
                // Checa duplicidade
                $stmt_check_despesa->bind_param("isd", $usuario_id, $data, $valor);
                $stmt_check_despesa->execute();

                if ($stmt_check_despesa->get_result()->num_rows > 0) {
                    $ignorados++; // Já existe
                } else {
                    // Insere
                    $stmt_insert_despesa->bind_param("isdis", $usuario_id, $descricao, $valor, $categoria_id, $data);
                    if ($stmt_insert_despesa->execute()) $cadastrados++;
                }
            }
        }

        // Liberar a memória fechando as conexões
        if ($stmt_check_renda) $stmt_check_renda->close();
        if ($stmt_check_despesa) $stmt_check_despesa->close();
        if ($stmt_insert_renda) $stmt_insert_renda->close();
        if ($stmt_insert_despesa) $stmt_insert_despesa->close();

        // RETORNO PARA A DASHBOARD
        $_SESSION['resposta'] = "Extrato lido! $cadastrados lançamentos salvos e $ignorados ignorados (duplicados ou fora de $ano_atual).";
        header($redirecionamento);
        exit;
    } catch (Exception $erro) {
        // Tratamento de erro seguro
        $_SESSION['resposta'] = "Erro inesperado ao processar o extrato. Tente novamente.";
        header($redirecionamento);
        exit;
    }
} else {
    // Redirecionamento se não for POST
    $_SESSION['resposta'] = "Método de solicitação inválido!";
    header("Location: " . BASE_URL . "dashboard");
    exit;
}
