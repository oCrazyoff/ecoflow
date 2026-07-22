<?php
/**
 * includes/dashboard/queries.php
 * Centraliza todas as consultas SQL para a dashboard
 */

$MESES_NOMES = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

$MESES_CURTOS = [
    1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
    5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
    9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'
];

$CORES_CATEGORIAS = ['#4BC0C0', '#36A2EB', '#FFCE56', '#FF6384', '#9966FF', '#FF9F40', '#C9CBCF'];

/**
 * Retorna o mês selecionado (via GET ou mês atual)
 */
function dashGetMes(): int
{
    if (isset($_GET['m']) && is_numeric($_GET['m']) && $_GET['m'] > 0 && $_GET['m'] < 13) {
        return (int)$_GET['m'];
    }
    return (int)date('m');
}

/**
 * Total de rendas do mês
 */
function totalRendas($mes = null, $ano = null): float
{
    global $conexao;
    $mes = $mes ?? dashGetMes();
    $ano = $ano ?? (int)date('Y');

    $sql = "SELECT COALESCE(SUM(valor), 0) FROM rendas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return (float)$valor;
}

/**
 * Total de despesas pagas do mês (regime de caixa)
 * Usa data_pagamento quando disponível para contabilizar quando o dinheiro realmente saiu.
 * Inclui: despesas normais pagas neste mês + adiantamentos feitos neste mês
 * Exclui: despesas pagas antecipadamente (status=2) cujo pagamento foi em outro mês
 */
function despesasPagas($mes = null, $ano = null): float
{
    global $conexao;
    $mes = $mes ?? dashGetMes();
    $ano = $ano ?? (int)date('Y');

    // Regime de caixa: soma despesas cujo pagamento efetivo ocorreu neste mês
    // Usa COALESCE(data_pagamento, data) para compatibilidade com dados antigos
    $sql = "SELECT COALESCE(SUM(valor), 0) FROM despesas 
            WHERE usuario_id = ? 
            AND status IN (1, 2) 
            AND MONTH(COALESCE(data_pagamento, data)) = ? 
            AND YEAR(COALESCE(data_pagamento, data)) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return (float)$valor;
}

/**
 * Total de despesas pendentes do mês
 * Exclui despesas pagas antecipadamente (status=2) — elas já foram contabilizadas
 */
function despesasPendentes($mes = null, $ano = null): float
{
    global $conexao;
    $mes = $mes ?? dashGetMes();
    $ano = $ano ?? (int)date('Y');

    $sql = "SELECT COALESCE(SUM(valor), 0) FROM despesas WHERE usuario_id = ? AND status = 0 AND tipo = 0 AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return (float)$valor;
}

/**
 * Total de TODAS as despesas do mês (pagas + pendentes)
 * Exclui lançamentos de adiantamento (tipo=1) para evitar dupla contagem
 */
function totalDespesas($mes = null, $ano = null): float
{
    global $conexao;
    $mes = $mes ?? dashGetMes();
    $ano = $ano ?? (int)date('Y');

    $sql = "SELECT COALESCE(SUM(valor), 0) FROM despesas WHERE usuario_id = ? AND tipo = 0 AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($valor);
    $stmt->fetch();
    $stmt->close();
    return (float)$valor;
}

/**
 * Dados para comparação com mês anterior
 */
function getDadosComparacao(): array
{
    $mes = dashGetMes();
    $ano = (int)date('Y');

    // Mês anterior
    $mesAnterior = $mes - 1;
    $anoAnterior = $ano;
    if ($mesAnterior < 1) {
        $mesAnterior = 12;
        $anoAnterior--;
    }

    $rendas_atual = totalRendas($mes, $ano);
    $rendas_anterior = totalRendas($mesAnterior, $anoAnterior);

    $despesas_atual = totalDespesas($mes, $ano);
    $despesas_anterior = totalDespesas($mesAnterior, $anoAnterior);

    $desp_pagas = despesasPagas($mes, $ano);
    $desp_pendentes = despesasPendentes($mes, $ano);

    $saldo_atual = $rendas_atual - $despesas_atual;
    $saldo_anterior = $rendas_anterior - $despesas_anterior;

    $eco_pct_atual = $rendas_atual > 0 ? round(($saldo_atual / $rendas_atual) * 100, 1) : 0;
    $eco_pct_anterior = $rendas_anterior > 0 ? round(($saldo_anterior / $rendas_anterior) * 100, 1) : 0;

    // Variações percentuais
    $var_rendas = $rendas_anterior > 0
        ? round((($rendas_atual - $rendas_anterior) / $rendas_anterior) * 100, 1)
        : ($rendas_atual > 0 ? 100 : 0);

    $var_despesas = $despesas_anterior > 0
        ? round((($despesas_atual - $despesas_anterior) / $despesas_anterior) * 100, 1)
        : ($despesas_atual > 0 ? 100 : 0);

    $var_saldo = $saldo_anterior != 0
        ? round((($saldo_atual - $saldo_anterior) / abs($saldo_anterior)) * 100, 1)
        : ($saldo_atual != 0 ? 100 : 0);

    $var_economia = round($eco_pct_atual - $eco_pct_anterior, 1);

    return [
        'rendas' => $rendas_atual,
        'despesas' => $despesas_atual,
        'saldo' => $saldo_atual,
        'economia_pct' => $eco_pct_atual,
        'despesas_pagas' => $desp_pagas,
        'despesas_pendentes' => $desp_pendentes,
        'var_rendas' => $var_rendas,
        'var_despesas' => $var_despesas,
        'var_saldo' => $var_saldo,
        'var_economia' => $var_economia,
    ];
}

/**
 * Categorias de despesas com totais e porcentagens
 */
function getCategoriasDespesas(): array
{
    global $conexao;
    $mes = dashGetMes();
    $ano = (int)date('Y');

    $sql = "SELECT c.nome, SUM(d.valor) AS total
            FROM despesas d
            JOIN categorias c ON d.categoria_id = c.id
            WHERE d.usuario_id = ? AND MONTH(d.data) = ? AND YEAR(d.data) = ?
            GROUP BY d.categoria_id, c.nome
            ORDER BY total DESC";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $categorias = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $totalGeral = array_sum(array_column($categorias, 'total'));

    foreach ($categorias as &$cat) {
        $cat['percentual'] = $totalGeral > 0 ? round(($cat['total'] / $totalGeral) * 100, 1) : 0;
    }

    return ['categorias' => $categorias, 'total' => $totalGeral];
}

/**
 * Dados do calendário financeiro (por dia)
 */
function getCalendarioFinanceiro(): array
{
    global $conexao;
    $mes = dashGetMes();
    $ano = (int)date('Y');

    // Rendas por dia
    $sql = "SELECT DAY(data) as dia, SUM(valor) as total FROM rendas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? GROUP BY DAY(data)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $rendas = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $rendas[(int)$row['dia']] = (float)$row['total'];
    $stmt->close();

    // Despesas normais por dia (sem parcela, sem recorrente)
    $sql = "SELECT DAY(data) as dia, SUM(valor) as total FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? AND parcela_grupo IS NULL AND recorrente = 0 GROUP BY DAY(data)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $despesas = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $despesas[(int)$row['dia']] = (float)$row['total'];
    $stmt->close();

    // Dias com parcelas
    $sql = "SELECT DISTINCT DAY(data) as dia FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? AND parcela_grupo IS NOT NULL";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $parcelas = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $parcelas[] = (int)$row['dia'];
    $stmt->close();

    // Dias com recorrentes
    $sql = "SELECT DISTINCT DAY(data) as dia FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? AND recorrente = 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $recorrentes = [];
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $recorrentes[] = (int)$row['dia'];
    $stmt->close();

    return [
        'rendas' => $rendas,
        'despesas' => $despesas,
        'parcelas' => $parcelas,
        'recorrentes' => $recorrentes,
        'mes' => $mes,
        'ano' => $ano,
        'num_dias' => cal_days_in_month(CAL_GREGORIAN, $mes, $ano),
        'primeiro_dia' => (int)date('w', mktime(0, 0, 0, $mes, 1, $ano))
    ];
}

/**
 * Histórico de receitas e despesas dos últimos 6 meses
 */
function getHistorico6Meses(): array
{
    global $MESES_CURTOS;
    $mes = dashGetMes();
    $ano = (int)date('Y');
    $dados = [];

    for ($i = 5; $i >= 0; $i--) {
        $m = $mes - $i;
        $a = $ano;
        while ($m < 1) {
            $m += 12;
            $a--;
        }

        $dados[] = [
            'mes' => $MESES_CURTOS[$m],
            'receitas' => totalRendas($m, $a),
            'despesas' => totalDespesas($m, $a)
        ];
    }

    return $dados;
}

/**
 * Gasto por semana do mês
 */
function getGastoPorSemana(): array
{
    global $conexao;
    $mes = dashGetMes();
    $ano = (int)date('Y');

    $semanas = [
        ['label' => 'Semana 1', 'periodo' => '1 — 7', 'total' => 0],
        ['label' => 'Semana 2', 'periodo' => '8 — 14', 'total' => 0],
        ['label' => 'Semana 3', 'periodo' => '15 — 21', 'total' => 0],
        ['label' => 'Semana 4', 'periodo' => '22+', 'total' => 0],
    ];

    $sql = "SELECT DAY(data) as dia, SUM(valor) as total FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? GROUP BY DAY(data)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $dia = (int)$row['dia'];
        if ($dia <= 7) $semanas[0]['total'] += (float)$row['total'];
        elseif ($dia <= 14) $semanas[1]['total'] += (float)$row['total'];
        elseif ($dia <= 21) $semanas[2]['total'] += (float)$row['total'];
        else $semanas[3]['total'] += (float)$row['total'];
    }
    $stmt->close();

    return $semanas;
}

/**
 * Resumo das parcelas
 */
function getResumoParcelas(): array
{
    global $conexao;
    $mes = dashGetMes();
    $ano = (int)date('Y');

    // Parcelas neste mês
    $sql = "SELECT COUNT(*) as qtd, COALESCE(SUM(valor), 0) as valor
            FROM despesas
            WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? AND parcela_grupo IS NOT NULL";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $parcelas_mes = (int)$result['qtd'];
    $valor_mes = (float)$result['valor'];
    $stmt->close();

    // Parcelas futuras (após o mês selecionado)
    $sql = "SELECT COUNT(*) as restantes, COALESCE(SUM(valor), 0) as saldo
            FROM despesas
            WHERE usuario_id = ? AND parcela_grupo IS NOT NULL
            AND ((YEAR(data) = ? AND MONTH(data) > ?) OR YEAR(data) > ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iiii", $_SESSION['id'], $ano, $mes, $ano);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $parcelas_restantes = (int)$result['restantes'];
    $saldo_restante = (float)$result['saldo'];
    $stmt->close();

    return [
        'parcelas_mes' => $parcelas_mes,
        'valor_mes' => $valor_mes,
        'parcelas_restantes' => $parcelas_restantes,
        'saldo_restante' => $saldo_restante
    ];
}

/**
 * Recordes do mês
 */
function getRecordes(): array
{
    global $conexao;
    $mes = dashGetMes();
    $ano = (int)date('Y');

    // Maior despesa
    $sql = "SELECT descricao, valor FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? ORDER BY valor DESC LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $maior_despesa = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Maior renda
    $sql = "SELECT descricao, valor FROM rendas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? ORDER BY valor DESC LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $maior_renda = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Dia com mais gasto
    $sql = "SELECT data, SUM(valor) as total FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? GROUP BY data ORDER BY total DESC LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $maior_dia = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return [
        'maior_despesa' => $maior_despesa,
        'maior_renda' => $maior_renda,
        'maior_dia' => $maior_dia
    ];
}

/**
 * Indicadores rápidos
 */
function getIndicadores(): array
{
    global $conexao;
    $mes = dashGetMes();
    $ano = (int)date('Y');

    // Maior categoria
    $sql = "SELECT c.nome FROM despesas d JOIN categorias c ON d.categoria_id = c.id
            WHERE d.usuario_id = ? AND MONTH(d.data) = ? AND YEAR(d.data) = ?
            GROUP BY d.categoria_id, c.nome ORDER BY SUM(d.valor) DESC LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($maior_cat);
    $stmt->fetch();
    $stmt->close();

    // Maior compra
    $sql = "SELECT descricao FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ? ORDER BY valor DESC LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($maior_compra);
    $stmt->fetch();
    $stmt->close();

    // Dia com mais gasto
    $sql = "SELECT DATE_FORMAT(data, '%d/%m') as dia_fmt FROM despesas
            WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?
            GROUP BY data ORDER BY SUM(valor) DESC LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($dia_gasto);
    $stmt->fetch();
    $stmt->close();

    // Total de lançamentos (rendas + despesas)
    $sql = "SELECT
            (SELECT COUNT(*) FROM rendas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?) +
            (SELECT COUNT(*) FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?) AS total";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iiiiii", $_SESSION['id'], $mes, $ano, $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($total_lancamentos);
    $stmt->fetch();
    $stmt->close();

    // Despesa média
    $sql = "SELECT COALESCE(AVG(valor), 0) FROM despesas WHERE usuario_id = ? AND MONTH(data) = ? AND YEAR(data) = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iii", $_SESSION['id'], $mes, $ano);
    $stmt->execute();
    $stmt->bind_result($despesa_media);
    $stmt->fetch();
    $stmt->close();

    return [
        'maior_categoria' => $maior_cat ?? '—',
        'maior_compra' => $maior_compra ?? '—',
        'dia_mais_gasto' => $dia_gasto ?? '—',
        'total_lancamentos' => (int)$total_lancamentos,
        'despesa_media' => round((float)$despesa_media, 2)
    ];
}
