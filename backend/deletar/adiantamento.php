<?php
/**
 * Endpoint: deletar/adiantamento.php
 * 
 * Cancela um adiantamento de despesa recorrente.
 * Recebe via POST (JSON):
 *   - id: ID do lançamento de adiantamento OU da despesa de competência
 */
require_once __DIR__ . '/../valida.php';

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents("php://input"), true);
$id = (int)($dados['id'] ?? 0);
$usuario_id = $_SESSION['id'];

if (!$id) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'ID inválido.']);
    exit;
}

// Busca a despesa informada
$sql = "SELECT id, tipo, status, adiantamento_ref_id FROM despesas WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$despesa = $resultado->fetch_assoc();
$stmt->close();

if (!$despesa) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Despesa não encontrada.']);
    exit;
}

// Determina qual é o adiantamento e qual é a competência
$idAdiantamento = null;
$idCompetencia = null;

if ($despesa['tipo'] == 1) {
    // Clicou no lançamento de adiantamento
    $idAdiantamento = $despesa['id'];
    $idCompetencia = $despesa['adiantamento_ref_id'];
} elseif ($despesa['status'] == 2) {
    // Clicou na despesa de competência (paga antecipadamente)
    $idCompetencia = $despesa['id'];
    $idAdiantamento = $despesa['adiantamento_ref_id'];
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Esta despesa não é um adiantamento.']);
    exit;
}

// =========================================================================
// CANCELAMENTO (Transação atômica)
// =========================================================================

$conexao->begin_transaction();

try {
    // Deletar o lançamento de adiantamento
    if ($idAdiantamento) {
        // Primeiro, limpar a referência na despesa de competência (para não violar FK)
        if ($idCompetencia) {
            $sqlLimpa = "UPDATE despesas SET adiantamento_ref_id = NULL WHERE id = ? AND usuario_id = ?";
            $stmtL = $conexao->prepare($sqlLimpa);
            $stmtL->bind_param("ii", $idCompetencia, $usuario_id);
            $stmtL->execute();
            $stmtL->close();
        }

        $sqlDel = "DELETE FROM despesas WHERE id = ? AND usuario_id = ? AND tipo = 1";
        $stmtDel = $conexao->prepare($sqlDel);
        $stmtDel->bind_param("ii", $idAdiantamento, $usuario_id);
        $stmtDel->execute();
        $stmtDel->close();
    }

    // Tratar a despesa de competência
    if ($idCompetencia) {
        // Verificar se a despesa de competência foi materializada pelo adiantamento
        // ou se já existia antes (geração automática de recorrência)
        $sqlCheckComp = "SELECT id, recorrente, MONTH(data) as mes_comp, YEAR(data) as ano_comp FROM despesas WHERE id = ? AND usuario_id = ?";
        $stmtCC = $conexao->prepare($sqlCheckComp);
        $stmtCC->bind_param("ii", $idCompetencia, $usuario_id);
        $stmtCC->execute();
        $resCC = $stmtCC->get_result();
        $competencia = $resCC->fetch_assoc();
        $stmtCC->close();

        if ($competencia) {
            $mesAtual = (int)date('n');
            $anoAtual = (int)date('Y');
            
            // Se o mês de competência é futuro (ainda não chegou), deleta a materialização
            if (($competencia['ano_comp'] > $anoAtual) || 
                ($competencia['ano_comp'] == $anoAtual && $competencia['mes_comp'] > $mesAtual)) {
                $sqlDelComp = "DELETE FROM despesas WHERE id = ? AND usuario_id = ?";
                $stmtDC = $conexao->prepare($sqlDelComp);
                $stmtDC->bind_param("ii", $idCompetencia, $usuario_id);
                $stmtDC->execute();
                $stmtDC->close();
            } else {
                // Se o mês já chegou, reverter para pendente
                $sqlRevert = "UPDATE despesas SET status = 0, data_pagamento = NULL, adiantamento_ref_id = NULL WHERE id = ? AND usuario_id = ?";
                $stmtRev = $conexao->prepare($sqlRevert);
                $stmtRev->bind_param("ii", $idCompetencia, $usuario_id);
                $stmtRev->execute();
                $stmtRev->close();
            }
        }
    }

    $conexao->commit();
    limparInsightsCache();

    echo json_encode(['sucesso' => true, 'mensagem' => 'Adiantamento cancelado com sucesso!']);

} catch (Exception $e) {
    $conexao->rollback();
    error_log("Erro ao cancelar adiantamento: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cancelar adiantamento. Tente novamente.']);
}
