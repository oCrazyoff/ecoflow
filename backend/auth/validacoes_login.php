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

        // Agora usa recorrencia_grupo para identificar séries (em vez de GROUP BY textual)
        $sqlRendas = "
            SELECT recorrencia_grupo, descricao, valor, MIN(data) as primeira_data
            FROM rendas
            WHERE usuario_id = ? AND YEAR(data) = ? AND recorrente = 1 AND recorrencia_grupo IS NOT NULL
            GROUP BY recorrencia_grupo, descricao, valor
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
                    $diaValido = date('t', strtotime("$anoAtual-$mes-01"));
                } else {
                    $diaValido = $diaOriginal;
                }

                $mesFormatado = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $dataVerificar = "$anoAtual-$mesFormatado-$diaValido";

                // Verifica se já existe (por recorrencia_grupo + mês, mais confiável que descrição)
                $sqlCheckR = "SELECT id FROM rendas WHERE usuario_id = ? AND recorrencia_grupo = ? AND YEAR(data) = ? AND MONTH(data) = ? LIMIT 1";
                $stmtCheckR = $conexao->prepare($sqlCheckR);
                $stmtCheckR->bind_param("isii", $userId, $itemRenda['recorrencia_grupo'], $anoAtual, $mes);
                $stmtCheckR->execute();
                $stmtCheckR->store_result();

                if ($stmtCheckR->num_rows === 0) {
                    $sqlInsertR = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, recorrencia_grupo, data) VALUES (?, ?, ?, 1, ?, ?)";
                    $stmtInsertR = $conexao->prepare($sqlInsertR);
                    $stmtInsertR->bind_param("isdss", $userId, $itemRenda['descricao'], $itemRenda['valor'], $itemRenda['recorrencia_grupo'], $dataVerificar);

                    if (!$stmtInsertR->execute()) {
                        throw new Exception("Erro ao inserir Renda: " . $stmtInsertR->error);
                    }
                    $stmtInsertR->close();
                }
                $stmtCheckR->close();
            }
        }
        $stmtRendas->close();

        // Fallback/Migração: rendas recorrentes SEM recorrencia_grupo (dados antigos)
        // Primeiro atribui UUID a todas, depois processa normalmente
        $sqlRendasSemGrupo = "
            SELECT descricao, valor, MIN(data) as primeira_data
            FROM rendas
            WHERE usuario_id = ? AND YEAR(data) = ? AND recorrente = 1 AND recorrencia_grupo IS NULL
            GROUP BY descricao, valor
        ";
        $stmtRendasSG = $conexao->prepare($sqlRendasSemGrupo);
        $stmtRendasSG->bind_param("ii", $userId, $anoAtual);
        $stmtRendasSG->execute();
        $resultRendasSG = $stmtRendasSG->get_result();

        while ($itemRenda = $resultRendasSG->fetch_assoc()) {
            // 1. Gerar UUID para este grupo
            $novoGrupo = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // 2. Atualizar TODAS as rendas existentes deste grupo para terem o UUID
            $sqlUpdateGrupo = "UPDATE rendas SET recorrencia_grupo = ? WHERE usuario_id = ? AND descricao = ? AND valor = ? AND recorrente = 1 AND recorrencia_grupo IS NULL AND YEAR(data) = ?";
            $stmtUpdateGrupo = $conexao->prepare($sqlUpdateGrupo);
            $stmtUpdateGrupo->bind_param("sisdi", $novoGrupo, $userId, $itemRenda['descricao'], $itemRenda['valor'], $anoAtual);
            $stmtUpdateGrupo->execute();
            $stmtUpdateGrupo->close();

            // 3. Agora inserir meses faltantes COM o UUID
            $primeiraData = new DateTime($itemRenda['primeira_data']);
            $mesInicial = (int)$primeiraData->format('m');
            $diaOriginal = $primeiraData->format('d');

            for ($mes = $mesInicial + 1; $mes <= $mesAtual; $mes++) {
                if (!checkdate($mes, $diaOriginal, $anoAtual)) {
                    $diaValido = date('t', strtotime("$anoAtual-$mes-01"));
                } else {
                    $diaValido = $diaOriginal;
                }

                $mesFormatado = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $dataVerificar = "$anoAtual-$mesFormatado-$diaValido";

                // Verificar por recorrencia_grupo + mês (mais confiável que descrição)
                $sqlCheckR = "SELECT id FROM rendas WHERE usuario_id = ? AND recorrencia_grupo = ? AND YEAR(data) = ? AND MONTH(data) = ? LIMIT 1";
                $stmtCheckR = $conexao->prepare($sqlCheckR);
                $stmtCheckR->bind_param("isii", $userId, $novoGrupo, $anoAtual, $mes);
                $stmtCheckR->execute();
                $stmtCheckR->store_result();

                if ($stmtCheckR->num_rows === 0) {
                    $sqlInsertR = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, recorrencia_grupo, data) VALUES (?, ?, ?, 1, ?, ?)";
                    $stmtInsertR = $conexao->prepare($sqlInsertR);
                    $stmtInsertR->bind_param("isdss", $userId, $itemRenda['descricao'], $itemRenda['valor'], $novoGrupo, $dataVerificar);

                    if (!$stmtInsertR->execute()) {
                        throw new Exception("Erro ao inserir Renda (migração grupo): " . $stmtInsertR->error);
                    }
                    $stmtInsertR->close();
                }
                $stmtCheckR->close();
            }
        }
        $stmtRendasSG->close();


        // ========== PARTE 2: PROCESSAR AS DESPESAS RECORRENTES ==========

        // Usa recorrencia_grupo para identificar séries
        $sqlDespesas = "
            SELECT recorrencia_grupo, descricao, valor, categoria_id, MIN(data) as primeira_data
            FROM despesas
            WHERE usuario_id = ? AND YEAR(data) = ? AND recorrente = 1 AND recorrencia_grupo IS NOT NULL AND tipo = 0
            GROUP BY recorrencia_grupo, descricao, valor, categoria_id
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

                // Verifica se já existe (por recorrencia_grupo + mês — inclui despesas materializadas por adiantamento)
                $sqlCheckD = "SELECT id FROM despesas WHERE usuario_id = ? AND recorrencia_grupo = ? AND YEAR(data) = ? AND MONTH(data) = ? AND tipo = 0 LIMIT 1";
                $stmtCheckD = $conexao->prepare($sqlCheckD);
                $stmtCheckD->bind_param("isii", $userId, $itemDespesa['recorrencia_grupo'], $anoAtual, $mes);
                $stmtCheckD->execute();
                $stmtCheckD->store_result();

                if ($stmtCheckD->num_rows === 0) {
                    $sqlInsertD = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data, recorrencia_grupo) VALUES (?, ?, ?, 0, 1, ?, ?, ?)";
                    $stmtInsertD = $conexao->prepare($sqlInsertD);
                    $stmtInsertD->bind_param("isdiss", $userId, $itemDespesa['descricao'], $itemDespesa['valor'], $itemDespesa['categoria_id'], $dataVerificar, $itemDespesa['recorrencia_grupo']);

                    if (!$stmtInsertD->execute()) {
                        throw new Exception("Erro ao inserir Despesa: " . $stmtInsertD->error);
                    }
                    $stmtInsertD->close();
                }
                $stmtCheckD->close();
            }
        }
        $stmtDespesas->close();

        // Fallback/Migração: despesas recorrentes SEM recorrencia_grupo (dados antigos)
        // Primeiro atribui UUID a todas, depois processa normalmente
        $sqlDespesasSemGrupo = "
            SELECT descricao, valor, categoria_id, MIN(data) as primeira_data
            FROM despesas
            WHERE usuario_id = ? AND YEAR(data) = ? AND recorrente = 1 AND recorrencia_grupo IS NULL
            GROUP BY descricao, valor, categoria_id
        ";

        $stmtDespesasSG = $conexao->prepare($sqlDespesasSemGrupo);
        $stmtDespesasSG->bind_param("ii", $userId, $anoAtual);
        $stmtDespesasSG->execute();
        $resultDespesasSG = $stmtDespesasSG->get_result();

        while ($itemDespesa = $resultDespesasSG->fetch_assoc()) {
            // 1. Gerar UUID para este grupo
            $novoGrupo = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            // 2. Atualizar TODAS as despesas existentes deste grupo para terem o UUID
            $sqlUpdateGrupo = "UPDATE despesas SET recorrencia_grupo = ? WHERE usuario_id = ? AND descricao = ? AND valor = ? AND categoria_id = ? AND recorrente = 1 AND recorrencia_grupo IS NULL AND YEAR(data) = ?";
            $stmtUpdateGrupo = $conexao->prepare($sqlUpdateGrupo);
            $stmtUpdateGrupo->bind_param("sisdii", $novoGrupo, $userId, $itemDespesa['descricao'], $itemDespesa['valor'], $itemDespesa['categoria_id'], $anoAtual);
            $stmtUpdateGrupo->execute();
            $stmtUpdateGrupo->close();

            // 3. Agora inserir meses faltantes COM o UUID
            $primeiraData = new DateTime($itemDespesa['primeira_data']);
            $mesInicial = (int)$primeiraData->format('m');
            $diaOriginal = $primeiraData->format('d');

            for ($mes = $mesInicial + 1; $mes <= $mesAtual; $mes++) {
                if (!checkdate($mes, $diaOriginal, $anoAtual)) {
                    $diaValido = date('t', strtotime("$anoAtual-$mes-01"));
                } else {
                    $diaValido = $diaOriginal;
                }

                $mesFormatado = str_pad($mes, 2, '0', STR_PAD_LEFT);
                $dataVerificar = "$anoAtual-$mesFormatado-$diaValido";

                // Verificar por recorrencia_grupo + mês (mais confiável que descrição)
                $sqlCheckD = "SELECT id FROM despesas WHERE usuario_id = ? AND recorrencia_grupo = ? AND YEAR(data) = ? AND MONTH(data) = ? AND tipo = 0 LIMIT 1";
                $stmtCheckD = $conexao->prepare($sqlCheckD);
                $stmtCheckD->bind_param("isii", $userId, $novoGrupo, $anoAtual, $mes);
                $stmtCheckD->execute();
                $stmtCheckD->store_result();

                if ($stmtCheckD->num_rows === 0) {
                    $sqlInsertD = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data, recorrencia_grupo) VALUES (?, ?, ?, 0, 1, ?, ?, ?)";
                    $stmtInsertD = $conexao->prepare($sqlInsertD);
                    $stmtInsertD->bind_param("isdiss", $userId, $itemDespesa['descricao'], $itemDespesa['valor'], $itemDespesa['categoria_id'], $dataVerificar, $novoGrupo);

                    if (!$stmtInsertD->execute()) {
                        throw new Exception("Erro ao inserir Despesa (migração grupo): " . $stmtInsertD->error);
                    }
                    $stmtInsertD->close();
                }
                $stmtCheckD->close();
            }
        }
        $stmtDespesasSG->close();

        // Se tudo deu certo
        $conexao->commit();
    } catch (Exception $e) {
        $conexao->rollback();
        logMsg("ERRO CRÍTICO (Rollback realizado): " . $e->getMessage());
    }
}
