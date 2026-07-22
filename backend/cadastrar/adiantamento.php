<?php
/**
 * Endpoint: cadastrar/adiantamento.php
 * 
 * Cria um adiantamento de despesa recorrente.
 * Recebe via POST (JSON):
 *   - despesa_id: ID da despesa recorrente de referência
 *   - mes_destino: Mês alvo (1-12)
 *   - ano_destino: Ano alvo (opcional, default = ano atual)
 */
require_once __DIR__ . '/../valida.php';

header('Content-Type: application/json; charset=utf-8');

$dados = json_decode(file_get_contents("php://input"), true);
$despesa_id = (int)($dados['despesa_id'] ?? 0);
$mes_destino = (int)($dados['mes_destino'] ?? 0);
$ano_destino = (int)($dados['ano_destino'] ?? date('Y'));
$usuario_id = $_SESSION['id'];

// =========================================================================
// VALIDAÇÕES
// =========================================================================

if (!$despesa_id || !$mes_destino || $mes_destino < 1 || $mes_destino > 12) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
    exit;
}

// Busca a despesa de referência
$sql = "SELECT id, descricao, valor, recorrente, categoria_id, recorrencia_grupo, data, tipo 
        FROM despesas WHERE id = ? AND usuario_id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("ii", $despesa_id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
$despesa = $resultado->fetch_assoc();
$stmt->close();

if (!$despesa) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Despesa não encontrada.']);
    exit;
}

if ($despesa['recorrente'] != 1) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Apenas despesas recorrentes podem ser adiantadas.']);
    exit;
}

if ($despesa['tipo'] == 1) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Esta despesa já é um lançamento de adiantamento.']);
    exit;
}

// Verificar limite: máximo de 6 meses à frente
$mesAtual = (int)date('n');
$anoAtual = (int)date('Y');
$mesesFrente = (($ano_destino - $anoAtual) * 12) + ($mes_destino - $mesAtual);

if ($mesesFrente < 1) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O mês de destino deve ser futuro.']);
    exit;
}

if ($mesesFrente > 6) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O adiantamento é limitado a 6 meses à frente.']);
    exit;
}

// Verificar se já existe despesa no mês destino para este grupo de recorrência
$sqlCheck = "SELECT id, status FROM despesas 
             WHERE usuario_id = ? AND recorrencia_grupo = ? AND MONTH(data) = ? AND YEAR(data) = ? AND tipo = 0 LIMIT 1";
$stmtCheck = $conexao->prepare($sqlCheck);
$stmtCheck->bind_param("isii", $usuario_id, $despesa['recorrencia_grupo'], $mes_destino, $ano_destino);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();
$despesaExistente = $resCheck->fetch_assoc();
$stmtCheck->close();

if ($despesaExistente) {
    if ($despesaExistente['status'] == 1) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A despesa deste mês já está paga.']);
        exit;
    }
    if ($despesaExistente['status'] == 2) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Esta despesa já foi adiantada.']);
        exit;
    }
}

// Verificar se já existe um adiantamento para este mês destino
$sqlCheckAdiant = "SELECT id FROM despesas 
                   WHERE usuario_id = ? AND recorrencia_grupo = ? AND tipo = 1 
                   AND adiantamento_ref_id IN (
                       SELECT id FROM despesas WHERE MONTH(data) = ? AND YEAR(data) = ? AND tipo = 0
                   ) LIMIT 1";
// Alternativa mais simples: verificar via mês do destino já materializado
$sqlCheckAdiant2 = "SELECT id FROM despesas 
                    WHERE usuario_id = ? AND recorrencia_grupo = ? AND MONTH(data) = ? AND YEAR(data) = ? AND tipo = 0 AND status = 2 LIMIT 1";
$stmtCheckA = $conexao->prepare($sqlCheckAdiant2);
$stmtCheckA->bind_param("isii", $usuario_id, $despesa['recorrencia_grupo'], $mes_destino, $ano_destino);
$stmtCheckA->execute();
$stmtCheckA->store_result();
if ($stmtCheckA->num_rows > 0) {
    $stmtCheckA->close();
    echo json_encode(['sucesso' => false, 'mensagem' => 'Já existe um adiantamento para este mês.']);
    exit;
}
$stmtCheckA->close();

// =========================================================================
// CRIAÇÃO DO ADIANTAMENTO (Transação atômica)
// =========================================================================

$conexao->begin_transaction();

try {
    $hoje = date('Y-m-d');
    $diaOriginal = date('d', strtotime($despesa['data']));
    
    // Calcula a data de competência (mês destino, mantendo o dia original)
    if (!checkdate($mes_destino, (int)$diaOriginal, $ano_destino)) {
        $diaCompetencia = date('t', strtotime("$ano_destino-$mes_destino-01"));
    } else {
        $diaCompetencia = $diaOriginal;
    }
    $dataCompetencia = sprintf("%04d-%02d-%02d", $ano_destino, $mes_destino, $diaCompetencia);

    // Se já existe a despesa do mês destino (gerada previamente), atualiza ela
    if ($despesaExistente) {
        $sqlUpdateDestino = "UPDATE despesas SET status = 2, data_pagamento = ? WHERE id = ?";
        $stmtUD = $conexao->prepare($sqlUpdateDestino);
        $stmtUD->bind_param("si", $hoje, $despesaExistente['id']);
        $stmtUD->execute();
        $stmtUD->close();
        $idDespesaCompetencia = $despesaExistente['id'];
    } else {
        // Cria a despesa de competência (mês futuro) — materialização antecipada
        $statusPagoAntecipadamente = 2;
        $tipoNormal = 0;
        $recorrente = 1;
        $sqlCriaCompetencia = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data, recorrencia_grupo, data_pagamento, tipo) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtCC = $conexao->prepare($sqlCriaCompetencia);
        $stmtCC->bind_param("issiiisssi", 
            $usuario_id, 
            $despesa['descricao'], 
            $despesa['valor'], 
            $statusPagoAntecipadamente,
            $recorrente,
            $despesa['categoria_id'], 
            $dataCompetencia, 
            $despesa['recorrencia_grupo'], 
            $hoje,
            $tipoNormal
        );
        $stmtCC->execute();
        $idDespesaCompetencia = $conexao->insert_id;
        $stmtCC->close();
    }

    // Cria o lançamento de adiantamento (mês atual)
    $statusPago = 1;
    $tipoAdiantamento = 1;
    $recorrente = 0; // O lançamento de adiantamento NÃO é recorrente
    $sqlCriaAdiantamento = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria_id, data, recorrencia_grupo, data_pagamento, adiantamento_ref_id, tipo) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtCA = $conexao->prepare($sqlCriaAdiantamento);
    $stmtCA->bind_param("issiiisssii", 
        $usuario_id, 
        $despesa['descricao'], 
        $despesa['valor'], 
        $statusPago,
        $recorrente,
        $despesa['categoria_id'], 
        $hoje, 
        $despesa['recorrencia_grupo'], 
        $hoje,
        $idDespesaCompetencia,
        $tipoAdiantamento
    );
    $stmtCA->execute();
    $idAdiantamento = $conexao->insert_id;
    $stmtCA->close();

    // Atualiza a despesa de competência para apontar de volta para o adiantamento
    $sqlLinkBack = "UPDATE despesas SET adiantamento_ref_id = ? WHERE id = ?";
    $stmtLB = $conexao->prepare($sqlLinkBack);
    $stmtLB->bind_param("ii", $idAdiantamento, $idDespesaCompetencia);
    $stmtLB->execute();
    $stmtLB->close();

    $conexao->commit();
    limparInsightsCache();

    // Nomes dos meses para a resposta
    $nomesMeses = [1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',5=>'Maio',6=>'Junho',
                   7=>'Julho',8=>'Agosto',9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro'];
    $nomeMesDestino = $nomesMeses[$mes_destino] ?? $mes_destino;

    echo json_encode([
        'sucesso' => true, 
        'mensagem' => "Adiantamento realizado! {$despesa['descricao']} de $nomeMesDestino foi pago antecipadamente.",
        'id_adiantamento' => $idAdiantamento,
        'id_competencia' => $idDespesaCompetencia
    ]);

} catch (Exception $e) {
    $conexao->rollback();
    error_log("Erro ao criar adiantamento: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao processar adiantamento. Tente novamente.']);
}
