<?php
/**
 * Endpoint AJAX: backend/ia/obter_insight.php
 * 
 * Retorna o insight da IA para o mês solicitado.
 * Se já existe no cache (banco), retorna instantaneamente.
 * Se não existe, gera via OpenAI e salva no cache.
 * 
 * Parâmetros GET:
 *   - m: mês (1-12)
 */
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../valida.php';
require_once __DIR__ . '/../../api/ia.php';
require_once __DIR__ . '/../../includes/dashboard/queries.php';

$mes = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$dia = (int)date('d');
$ano = (int)date('Y');

// Validar mês
if ($mes < 1 || $mes > 12) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Mês inválido.']);
    exit;
}

// Calcular saldo para decidir o tipo de insight
$saldo = totalRendas($mes) - despesasPagas($mes);

// Buscar insight já salvo no banco
$sql_ia = "SELECT titulo, mensagem FROM insights WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?";
$stmt_ia = $conexao->prepare($sql_ia);
$stmt_ia->bind_param("iii", $_SESSION['id'], $mes, $ano);
$stmt_ia->execute();
$resultado_ia = $stmt_ia->get_result();
$dados_ia = $resultado_ia->fetch_assoc();
$stmt_ia->close();

// Variáveis de saída
$titulo_ia = '';
$txt_ia = '';
$expected_title_type = null; // 0=Meta, 1=Parabéns, 2=Alerta

if ($mes == date('m')) {
    // --- MÊS ATUAL ---
    if ($dia <= 15) {
        // Começo do mês: esperado é uma Meta
        $expected_title_type = 0;
    } else {
        // Fim do mês: esperado é Parabéns ou Alerta
        if ($saldo > 0) {
            $expected_title_type = 1; // Parabéns
        } elseif ($saldo < 0) {
            $expected_title_type = 2; // Alerta
        } else {
            // Saldo neutro (== 0): gerar parabéns com tom neutro
            $expected_title_type = 1;
        }
    }
} elseif ($mes < date('m')) {
    // --- MÊS PASSADO ---
    if ($saldo > 0) {
        $expected_title_type = 1; // Parabéns
    } elseif ($saldo < 0) {
        $expected_title_type = 2; // Alerta
    } else {
        $expected_title_type = 1; // Saldo neutro
    }
}
// Mês futuro: $expected_title_type permanece null, retornamos mensagem padrão

// Se não há estado esperado (mês futuro)
if ($expected_title_type === null) {
    echo json_encode([
        'sucesso' => true,
        'titulo' => 'Planejamento 📋',
        'mensagem' => 'Ainda não há dados suficientes para este mês. Continue registrando suas movimentações!'
    ]);
    exit;
}

// CASO 1: Insight salvo E o tipo dele bate com o esperado (e não é mensagem de erro)
if ($dados_ia && $dados_ia['titulo'] == $expected_title_type && stripos($dados_ia['mensagem'], 'Erro') !== 0) {
    $txt_ia = $dados_ia['mensagem'];

    if ($expected_title_type == 0) $titulo_ia = 'Meta Financeira 🎯';
    if ($expected_title_type == 1) $titulo_ia = 'Parabéns ✅';
    if ($expected_title_type == 2) $titulo_ia = 'Alerta ⚠️';

// CASO 2: Não há insight salvo OU o tipo não bate
} else {
    if ($expected_title_type == 0) {
        $titulo_ia = 'Meta Financeira 🎯';
        $txt_ia = gerarMeta($mes);
    } elseif ($expected_title_type == 1) {
        $titulo_ia = 'Parabéns ✅';
        $txt_ia = gerarSucesso($mes);
    } elseif ($expected_title_type == 2) {
        $titulo_ia = 'Alerta ⚠️';
        $txt_ia = gerarAlerta($mes);
    }
}

// Retornar resultado
$erro_tecnico = $GLOBALS['ultimo_erro_ia'] ?? null;

// Garante que nenhuma mensagem técnica de erro seja exposta como insight para o usuário
if (!empty(trim($txt_ia ?? '')) && stripos($txt_ia, 'Erro') !== 0) {
    echo json_encode([
        'sucesso' => true,
        'titulo' => $titulo_ia,
        'mensagem' => $txt_ia,
        'erro_tecnico' => $erro_tecnico
    ]);
} else {
    echo json_encode([
        'sucesso' => true,
        'titulo' => '',
        'mensagem' => '',
        'erro_tecnico' => $erro_tecnico
    ]);
}
