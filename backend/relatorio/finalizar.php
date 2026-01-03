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

    // Inicia a transação (Tudo ou nada)
    $conexao->begin_transaction();

    try {
        // =================================================================================
        // ETAPA 1: MIGRAR RENDAS RECORRENTES (De Dezembro/AnoPassado para Janeiro/AnoAtual)
        // =================================================================================

        $sqlGetRendas = "
            SELECT descricao, valor, DAY(data) as dia
            FROM rendas
            WHERE usuario_id = ? 
            AND recorrente = 1 
            AND YEAR(data) = ? 
            AND MONTH(data) = 12
        ";

        $stmtGetR = $conexao->prepare($sqlGetRendas);
        $stmtGetR->bind_param("ii", $userId, $anoPassado);
        $stmtGetR->execute();
        $resRendas = $stmtGetR->get_result();

        // Prepara o insert para reutilizar no loop
        $sqlInsRenda = "INSERT INTO rendas (usuario_id, descricao, valor, recorrente, data) VALUES (?, ?, ?, 1, ?)";
        $stmtInsR = $conexao->prepare($sqlInsRenda);

        while ($renda = $resRendas->fetch_assoc()) {
            // Cria a data para Janeiro do ano atual
            // Ex: Se era 15/12/2024 vira 15/01/2025
            $novaData = sprintf("%04d-01-%02d", $anoAtual, $renda['dia']);

            $stmtInsR->bind_param("isds", $userId, $renda['descricao'], $renda['valor'], $novaData);
            if (!$stmtInsR->execute()) {
                throw new Exception("Erro ao migrar renda: " . $stmtInsR->error);
            }
        }
        $stmtGetR->close();
        $stmtInsR->close();

        // ===================================================================================
        // ETAPA 2: MIGRAR DESPESAS RECORRENTES (De Dezembro/AnoPassado para Janeiro/AnoAtual)
        // ===================================================================================

        $sqlGetDespesas = "
            SELECT descricao, valor, categoria, DAY(data) as dia
            FROM despesas
            WHERE usuario_id = ? 
            AND recorrente = 1 
            AND YEAR(data) = ? 
            AND MONTH(data) = 12
        ";

        $stmtGetD = $conexao->prepare($sqlGetDespesas);
        $stmtGetD->bind_param("ii", $userId, $anoPassado);
        $stmtGetD->execute();
        $resDespesas = $stmtGetD->get_result();

        // Prepara o insert (Status entra como 0 = Pendente)
        $sqlInsDespesa = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria, data) VALUES (?, ?, ?, 0, 1, ?, ?)";
        $stmtInsD = $conexao->prepare($sqlInsDespesa);

        while ($despesa = $resDespesas->fetch_assoc()) {
            $novaData = sprintf("%04d-01-%02d", $anoAtual, $despesa['dia']);

            $stmtInsD->bind_param("isdis", $userId, $despesa['descricao'], $despesa['valor'], $despesa['categoria'], $novaData);
            if (!$stmtInsD->execute()) {
                throw new Exception("Erro ao migrar despesa: " . $stmtInsD->error);
            }
        }
        $stmtGetD->close();
        $stmtInsD->close();

        // =================================================================================
        // ETAPA 3: LIMPEZA DOS DADOS ANTIGOS (Mantém apenas o ano atual)
        // =================================================================================

        // Deleta despesas antigas (Ano < Ano Atual)
        $sqlDelDespesas = "DELETE FROM despesas WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDelD = $conexao->prepare($sqlDelDespesas);
        $stmtDelD->bind_param("ii", $userId, $anoAtual);
        $stmtDelD->execute();
        $stmtDelD->close();

        // Deleta rendas antigas
        $sqlDelRendas = "DELETE FROM rendas WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDelR = $conexao->prepare($sqlDelRendas);
        $stmtDelR->bind_param("ii", $userId, $anoAtual);
        $stmtDelR->execute();
        $stmtDelR->close();

        // Deleta insights antigos
        $sqlDelInsights = "DELETE FROM insights WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDelI = $conexao->prepare($sqlDelInsights);
        $stmtDelI->bind_param("ii", $userId, $anoAtual);
        $stmtDelI->execute();
        $stmtDelI->close();

        // Se chegou até aqui, confirma todas as alterações
        $conexao->commit();
        return true;
    } catch (Exception $e) {
        // Se algo deu errado, desfaz tudo (migrações e deletes)
        $conexao->rollback();
        // Opcional: logar o erro $e->getMessage()
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
