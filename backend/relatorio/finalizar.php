<?php
require_once __DIR__ . '/../conexao.php';

session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado.']);
    exit();
}

if (empty($_SESSION['relatorio_pendente'])) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Nenhum relatório pendente para finalizar.']);
    exit();
}

// Função unificada para migrar recorrentes e limpar dados antigos
function processarViradaDeAno(int $userId): bool
{
    global $conexao;

    $anoAtual = date('Y');
    $anoPassado = $anoAtual - 1;
    $mesAtual = (int) date('n'); // Retorna o mês atual de 1 a 12

    // Inicia a transação (Tudo ou nada)
    $conexao->begin_transaction();

    try {
        // =================================================================================
        // ETAPA 1: MIGRAR RENDAS RECORRENTES (Do último mês ativo do AnoPassado até Mês Atual)
        // =================================================================================

        // Descobre qual foi o último mês com renda recorrente no ano passado
        $sqlMaxRenda = "SELECT MAX(MONTH(data)) as ultimo_mes FROM rendas WHERE usuario_id = ? AND recorrente = 1 AND YEAR(data) = ?";
        $stmtMaxR = $conexao->prepare($sqlMaxRenda);
        $stmtMaxR->bind_param("ii", $userId, $anoPassado);
        $stmtMaxR->execute();
        $stmtMaxR->bind_result($ultimoMesRenda);
        $stmtMaxR->fetch();
        $stmtMaxR->close();

        // Só tenta migrar se encontrou algum mês com renda recorrente
        if ($ultimoMesRenda) {
            $sqlGetRendas = "
                SELECT descricao, valor, DAY(data) as dia
                FROM rendas
                WHERE usuario_id = ? 
                AND recorrente = 1 
                AND YEAR(data) = ? 
                AND MONTH(data) = ?
            ";

            $stmtGetR = $conexao->prepare($sqlGetRendas);
            $stmtGetR->bind_param("iii", $userId, $anoPassado, $ultimoMesRenda);
            $stmtGetR->execute();
            $resRendas = $stmtGetR->get_result();

            $sqlInsRenda = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, data) VALUES (?, ?, ?, 1, ?)";
            $stmtInsR = $conexao->prepare($sqlInsRenda);

            while ($renda = $resRendas->fetch_assoc()) {
                for ($m = 1; $m <= $mesAtual; $m++) {
                    $ultimoDiaMes = date('t', strtotime(sprintf("%04d-%02d-01", $anoAtual, $m)));
                    $diaReal = ($renda['dia'] > $ultimoDiaMes) ? $ultimoDiaMes : $renda['dia'];

                    $novaData = sprintf("%04d-%02d-%02d", $anoAtual, $m, $diaReal);

                    $stmtInsR->bind_param("isds", $userId, $renda['descricao'], $renda['valor'], $novaData);
                    if (!$stmtInsR->execute()) {
                        throw new Exception("Erro ao migrar renda: " . $stmtInsR->error);
                    }
                }
            }
            $stmtGetR->close();
            $stmtInsR->close();
        }

        // ===================================================================================
        // ETAPA 2: MIGRAR DESPESAS RECORRENTES (Do último mês ativo do AnoPassado até Mês Atual)
        // ===================================================================================

        // Descobre qual foi o último mês com despesa recorrente no ano passado
        $sqlMaxDespesa = "SELECT MAX(MONTH(data)) as ultimo_mes FROM despesas WHERE usuario_id = ? AND recorrente = 1 AND YEAR(data) = ?";
        $stmtMaxD = $conexao->prepare($sqlMaxDespesa);
        $stmtMaxD->bind_param("ii", $userId, $anoPassado);
        $stmtMaxD->execute();
        $stmtMaxD->bind_result($ultimoMesDespesa);
        $stmtMaxD->fetch();
        $stmtMaxD->close();

        // Só tenta migrar se encontrou algum mês com despesa recorrente
        if ($ultimoMesDespesa) {
            $sqlGetDespesas = "
                SELECT descricao, valor, categoria_id, DAY(data) as dia
                FROM despesas
                WHERE usuario_id = ? 
                AND recorrente = 1 
                AND YEAR(data) = ? 
                AND MONTH(data) = ?
            ";

            $stmtGetD = $conexao->prepare($sqlGetDespesas);
            $stmtGetD->bind_param("iii", $userId, $anoPassado, $ultimoMesDespesa);
            $stmtGetD->execute();
            $resDespesas = $stmtGetD->get_result();

            $sqlInsDespesa = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data) VALUES (?, ?, ?, 0, 1, ?, ?)";
            $stmtInsD = $conexao->prepare($sqlInsDespesa);

            while ($despesa = $resDespesas->fetch_assoc()) {
                for ($m = 1; $m <= $mesAtual; $m++) {
                    $ultimoDiaMes = date('t', strtotime(sprintf("%04d-%02d-01", $anoAtual, $m)));
                    $diaReal = ($despesa['dia'] > $ultimoDiaMes) ? $ultimoDiaMes : $despesa['dia'];

                    $novaData = sprintf("%04d-%02d-%02d", $anoAtual, $m, $diaReal);

                    $stmtInsD->bind_param("isdis", $userId, $despesa['descricao'], $despesa['valor'], $despesa['categoria_id'], $novaData);
                    if (!$stmtInsD->execute()) {
                        throw new Exception("Erro ao migrar despesa: " . $stmtInsD->error);
                    }
                }
            }
            $stmtGetD->close();
            $stmtInsD->close();
        }

        // =================================================================================
        // ETAPA 3: LIMPEZA DOS DADOS ANTIGOS (Mantém apenas o ano atual)
        // =================================================================================

        $sqlDelDespesas = "DELETE FROM despesas WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDelD = $conexao->prepare($sqlDelDespesas);
        $stmtDelD->bind_param("ii", $userId, $anoAtual);
        $stmtDelD->execute();
        $stmtDelD->close();

        $sqlDelRendas = "DELETE FROM rendas WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDelR = $conexao->prepare($sqlDelRendas);
        $stmtDelR->bind_param("ii", $userId, $anoAtual);
        $stmtDelR->execute();
        $stmtDelR->close();

        $sqlDelInsights = "DELETE FROM insights WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDelI = $conexao->prepare($sqlDelInsights);
        $stmtDelI->bind_param("ii", $userId, $anoAtual);
        $stmtDelI->execute();
        $stmtDelI->close();

        $conexao->commit();
        return true;
    } catch (Exception $e) {
        $conexao->rollback();
        return false;
    }
}

try {
    // Executa o processo completo (Migrar -> Limpar)
    $sucesso = processarViradaDeAno($_SESSION['id']);

    if (!$sucesso) {
        throw new Exception("Falha ao processar a virada de ano.");
    }

    // Atualiza a flag para liberar o acesso do usuário ao sistema
    $stmt = $conexao->prepare("UPDATE usuarios SET relatorio_anual_pendente = 0 WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['relatorio_pendente']);

    // Responde ao JavaScript com sucesso
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Ano finalizado e recorrentes migradas com sucesso.']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro no servidor.']);
}
