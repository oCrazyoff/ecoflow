<?php
function logMsg($msg)
{
    // Salva o log num arquivo 'debug_recorrentes.log' com data e hora
    $file = __DIR__ . '/debug_recorrentes.log';
    $time = date('d/m/Y H:i:s');
    file_put_contents($file, "[$time] $msg" . PHP_EOL, FILE_APPEND);
}

function verificarRecorrentes(int $userId): void
{
    global $conexao;

    $anoAtual = date('Y');
    $mesAtual = date('m');

    // Inicia transação
    $conexao->begin_transaction();

    try {
        // ========== PARTE 1: PROCESSAR AS RENDAS RECORRENTES ==========

        $sqlRendas = "
            SELECT descricao, valor, MIN(data) as primeira_data
            FROM rendas
            WHERE usuario_id = ? AND YEAR(data) = ? AND recorrente = 1
            GROUP BY descricao, valor
        ";

        $stmtRendas = $conexao->prepare($sqlRendas);
        $stmtRendas->bind_param("ii", $userId, $anoAtual);
        $stmtRendas->execute();
        $resultRendas = $stmtRendas->get_result();

        while ($itemRenda = $resultRendas->fetch_assoc()) {
            $primeiraData = new DateTime($itemRenda['primeira_data']);
            $mesInicial = (int)$primeiraData->format('m');
            $diaOriginal = $primeiraData->format('d');

            for ($mes = $mesInicial + 1; $mes <= $mesAtual; $mes++) {
                // Validação de data segura (evita erros como 31 de fevereiro)
                if (!checkdate($mes, $diaOriginal, $anoAtual)) {
                    // Se o dia não existe no mês (ex: dia 31 em abril), ajusta para o último dia do mês
                    $diaValido = date('t', strtotime("$anoAtual-$mes-01"));
                } else {
                    $diaValido = $diaOriginal;
                }

                $mesFormatado = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $dataVerificar = "$anoAtual-$mesFormatado-$diaValido";

                // Verifica se já existe
                $sqlCheckR = "SELECT id FROM rendas WHERE usuario_id = ? AND descricao = ? AND YEAR(data) = ? AND MONTH(data) = ? LIMIT 1";
                $stmtCheckR = $conexao->prepare($sqlCheckR);
                $stmtCheckR->bind_param("isii", $userId, $itemRenda['descricao'], $anoAtual, $mes);
                $stmtCheckR->execute();
                $stmtCheckR->store_result();

                if ($stmtCheckR->num_rows === 0) {

                    $sqlInsertR = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, data) VALUES (?, ?, ?, 1, ?)";
                    $stmtInsertR = $conexao->prepare($sqlInsertR);
                    $stmtInsertR->bind_param("isds", $userId, $itemRenda['descricao'], $itemRenda['valor'], $dataVerificar);

                    if (!$stmtInsertR->execute()) {
                        throw new Exception("Erro ao inserir Renda: " . $stmtInsertR->error);
                    }
                    $stmtInsertR->close();
                }
                $stmtCheckR->close();
            }
        }
        $stmtRendas->close();


        // ========== PARTE 2: PROCESSAR AS DESPESAS RECORRENTES ==========

        $sqlDespesas = "
            SELECT descricao, valor, categoria, MIN(data) as primeira_data
            FROM despesas
            WHERE usuario_id = ? AND YEAR(data) = ? AND recorrente = 1
            GROUP BY descricao, valor, categoria
        ";

        $stmtDespesas = $conexao->prepare($sqlDespesas);
        $stmtDespesas->bind_param("ii", $userId, $anoAtual);
        $stmtDespesas->execute();
        $resultDespesas = $stmtDespesas->get_result();

        while ($itemDespesa = $resultDespesas->fetch_assoc()) {
            $primeiraData = new DateTime($itemDespesa['primeira_data']);
            $mesInicial = (int)$primeiraData->format('m');
            $diaOriginal = $primeiraData->format('d');

            for ($mes = $mesInicial + 1; $mes <= $mesAtual; $mes++) {
                // Validação de data segura
                if (!checkdate($mes, $diaOriginal, $anoAtual)) {
                    $diaValido = date('t', strtotime("$anoAtual-$mes-01"));
                } else {
                    $diaValido = $diaOriginal;
                }

                $mesFormatado = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $dataVerificar = "$anoAtual-$mesFormatado-$diaValido";

                // Verifica se já existe
                $sqlCheckD = "SELECT id FROM despesas WHERE usuario_id = ? AND descricao = ? AND YEAR(data) = ? AND MONTH(data) = ? LIMIT 1";
                $stmtCheckD = $conexao->prepare($sqlCheckD);
                $stmtCheckD->bind_param("isii", $userId, $itemDespesa['descricao'], $anoAtual, $mes);
                $stmtCheckD->execute();
                $stmtCheckD->store_result();

                if ($stmtCheckD->num_rows === 0) {

                    $sqlInsertD = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria, data) VALUES (?, ?, ?, 0, 1, ?, ?)";
                    $stmtInsertD = $conexao->prepare($sqlInsertD);
                    $stmtInsertD->bind_param("isdis", $userId, $itemDespesa['descricao'], $itemDespesa['valor'], $itemDespesa['categoria'], $dataVerificar);

                    if (!$stmtInsertD->execute()) {
                        throw new Exception("Erro ao inserir Despesa: " . $stmtInsertD->error);
                    }
                    $stmtInsertD->close();
                }
                $stmtCheckD->close();
            }
        }
        $stmtDespesas->close();

        // Se tudo deu certo
        $conexao->commit();
    } catch (Exception $e) {
        $conexao->rollback();
        logMsg("ERRO CRÍTICO (Rollback realizado): " . $e->getMessage());
    }
}
