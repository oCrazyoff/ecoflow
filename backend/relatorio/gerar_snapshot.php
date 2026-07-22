<?php
require_once __DIR__ . '/../conexao.php';

/**
 * Gera o snapshot (relatório anual) de um determinado ano para o usuário.
 * Retorna true em caso de sucesso, false caso contrário.
 */
function gerarRelatorioAnual($usuario_id, $ano) {
    global $conexao;

    // 1. Coleta Totais de Rendas e Despesas
    $sqlTotais = "SELECT 
        (SELECT COALESCE(SUM(valor), 0) FROM rendas WHERE usuario_id = ? AND YEAR(data) = ?) as total_rendas,
        (SELECT COALESCE(SUM(valor), 0) FROM despesas WHERE usuario_id = ? AND status IN (1, 2) AND tipo = 0 AND YEAR(data) = ?) as total_despesas_pagas,
        (SELECT COALESCE(SUM(valor), 0) FROM despesas WHERE usuario_id = ? AND status = 0 AND tipo = 0 AND YEAR(data) = ?) as total_despesas_pendentes
    ";
    
    $stmt = $conexao->prepare($sqlTotais);
    $stmt->bind_param("iiiiii", $usuario_id, $ano, $usuario_id, $ano, $usuario_id, $ano);
    $stmt->execute();
    $stmt->bind_result($receitas_total, $despesas_pagas, $despesas_pendentes);
    $stmt->fetch();
    $stmt->close();
    
    $despesas_total = $despesas_pagas + $despesas_pendentes;
    $saldo_total = $receitas_total - $despesas_total;

    // Se não há movimentação nenhuma no ano, não precisa gerar relatório, ou gera vazio.
    if ($receitas_total == 0 && $despesas_total == 0) {
        return true; // Nada a salvar
    }

    // 2. Coleta dados completos para o JSON
    $json_data = [
        'ano' => $ano,
        'totais' => [
            'receitas' => $receitas_total,
            'despesas_pagas' => $despesas_pagas,
            'despesas_pendentes' => $despesas_pendentes,
            'despesas_total' => $despesas_total,
            'saldo' => $saldo_total
        ],
        'meses' => []
    ];

    // Para cada mês, agrupar rendas e despesas
    for ($mes = 1; $mes <= 12; $mes++) {
        $mes_data = ['rendas' => [], 'despesas' => []];
        $tem_dados = false;

        // Rendas do mês
        $sql_rendas = "SELECT descricao, data, valor FROM rendas WHERE usuario_id = ? AND YEAR(data) = ? AND MONTH(data) = ? ORDER BY data ASC";
        $stmt_r = $conexao->prepare($sql_rendas);
        $stmt_r->bind_param("iii", $usuario_id, $ano, $mes);
        $stmt_r->execute();
        $res_r = $stmt_r->get_result();
        while ($r = $res_r->fetch_assoc()) {
            $mes_data['rendas'][] = $r;
            $tem_dados = true;
        }
        $stmt_r->close();

        // Despesas do mês
        $sql_desp = "SELECT d.descricao, d.data, d.valor, d.status, d.tipo, c.nome as categoria_nome 
                     FROM despesas d 
                     LEFT JOIN categorias c ON d.categoria_id = c.id 
                     WHERE d.usuario_id = ? AND YEAR(d.data) = ? AND MONTH(d.data) = ? ORDER BY d.data ASC";
        $stmt_d = $conexao->prepare($sql_desp);
        $stmt_d->bind_param("iii", $usuario_id, $ano, $mes);
        $stmt_d->execute();
        $res_d = $stmt_d->get_result();
        while ($d = $res_d->fetch_assoc()) {
            $mes_data['despesas'][] = $d;
            $tem_dados = true;
        }
        $stmt_d->close();

        if ($tem_dados) {
            $json_data['meses'][$mes] = $mes_data;
        }
    }

    $json_string = json_encode($json_data, JSON_UNESCAPED_UNICODE);

    // 3. Salvar no banco
    $status = 'GERADO';
    $versao = 1;

    $sqlInsert = "INSERT INTO relatorios_anuais 
        (usuario_id, ano, receitas_total, despesas_total, saldo_total, dados_json, versao, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_ins = $conexao->prepare($sqlInsert);
    $stmt_ins->bind_param("iidddsis", $usuario_id, $ano, $receitas_total, $despesas_total, $saldo_total, $json_string, $versao, $status);
    
    $sucesso = $stmt_ins->execute();
    $stmt_ins->close();

    return $sucesso;
}

/**
 * Função para migrar as recorrentes de um ano para o outro
 * Igual à lógica que já existia no finalizar.php
 */
function migrarRecorrentesParaAnoAtual($userId, $anoPassado, $anoAtual, $mesAtual) {
    global $conexao;

    $conexao->begin_transaction();

    try {
        // MIGRAR RENDAS
        $sqlMaxRenda = "SELECT MAX(MONTH(data)) as ultimo_mes FROM rendas WHERE usuario_id = ? AND recorrente = 1 AND YEAR(data) = ?";
        $stmtMaxR = $conexao->prepare($sqlMaxRenda);
        $stmtMaxR->bind_param("ii", $userId, $anoPassado);
        $stmtMaxR->execute();
        $stmtMaxR->bind_result($ultimoMesRenda);
        $stmtMaxR->fetch();
        $stmtMaxR->close();

        if ($ultimoMesRenda) {
            $sqlGetRendas = "SELECT descricao, valor, DAY(data) as dia FROM rendas WHERE usuario_id = ? AND recorrente = 1 AND YEAR(data) = ? AND MONTH(data) = ?";
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
                    $stmtInsR->execute();
                }
            }
            $stmtGetR->close();
            $stmtInsR->close();
        }

        // MIGRAR DESPESAS
        $sqlMaxDespesa = "SELECT MAX(MONTH(data)) as ultimo_mes FROM despesas WHERE usuario_id = ? AND recorrente = 1 AND YEAR(data) = ?";
        $stmtMaxD = $conexao->prepare($sqlMaxDespesa);
        $stmtMaxD->bind_param("ii", $userId, $anoPassado);
        $stmtMaxD->execute();
        $stmtMaxD->bind_result($ultimoMesDespesa);
        $stmtMaxD->fetch();
        $stmtMaxD->close();

        if ($ultimoMesDespesa) {
            $sqlGetDespesas = "SELECT descricao, valor, categoria, DAY(data) as dia FROM despesas WHERE usuario_id = ? AND recorrente = 1 AND YEAR(data) = ? AND MONTH(data) = ?";
            $stmtGetD = $conexao->prepare($sqlGetDespesas);
            $stmtGetD->bind_param("iii", $userId, $anoPassado, $ultimoMesDespesa);
            $stmtGetD->execute();
            $resDespesas = $stmtGetD->get_result();

            $sqlInsDespesa = "INSERT INTO despesas (usuario_id, descricao, valor, status, recorrente, categoria, data) VALUES (?, ?, ?, 0, 1, ?, ?)";
            $stmtInsD = $conexao->prepare($sqlInsDespesa);

            while ($despesa = $resDespesas->fetch_assoc()) {
                for ($m = 1; $m <= $mesAtual; $m++) {
                    $ultimoDiaMes = date('t', strtotime(sprintf("%04d-%02d-01", $anoAtual, $m)));
                    $diaReal = ($despesa['dia'] > $ultimoDiaMes) ? $ultimoDiaMes : $despesa['dia'];
                    $novaData = sprintf("%04d-%02d-%02d", $anoAtual, $m, $diaReal);

                    $stmtInsD->bind_param("isdis", $userId, $despesa['descricao'], $despesa['valor'], $despesa['categoria'], $novaData);
                    $stmtInsD->execute();
                }
            }
            $stmtGetD->close();
            $stmtInsD->close();
        }

        $conexao->commit();
        return true;
    } catch (Exception $e) {
        $conexao->rollback();
        return false;
    }
}

/**
 * Limpa dados com mais de 2 anos de idade em relação ao ano atual,
 * DEPOIS de verificar que os relatórios dos anos sendo apagados 
 * já foram gerados.
 */
function limparDadosAntigosSeguro($usuario_id) {
    global $conexao;
    $anoAtual = (int)date('Y');
    
    // O ano limite para manter é anoAtual - 1 (ex: se hoje é 2027, mantemos 2026. Apagamos 2025 ou mais velhos).
    // Mas a regra pediu "mais de dois anos": 2026 e 2027 disponíveis. Entra 2029, remove 2026. 
    // Entra 2027 -> 2026 e 2027 mantidos. 2025 é removido. (anoAtual - 2)
    $anoCorte = $anoAtual - 2; 

    // Primeiro, vamos ver quais anos antes ou igual ao anoCorte ainda têm dados.
    // E só deletar os anos que JÁ TÊM relatório com status 'GERADO'.
    $sqlAnos = "SELECT DISTINCT YEAR(data) as ano FROM despesas WHERE usuario_id = ? AND YEAR(data) <= ?
                UNION 
                SELECT DISTINCT YEAR(data) as ano FROM rendas WHERE usuario_id = ? AND YEAR(data) <= ?";
    
    $stmtA = $conexao->prepare($sqlAnos);
    $stmtA->bind_param("iiii", $usuario_id, $anoCorte, $usuario_id, $anoCorte);
    $stmtA->execute();
    $resA = $stmtA->get_result();
    
    while ($row = $resA->fetch_assoc()) {
        $anoCheck = $row['ano'];
        
        // Verifica se existe snapshot GERADO para este ano
        $sqlCheck = "SELECT id FROM relatorios_anuais WHERE usuario_id = ? AND ano = ? AND status = 'GERADO'";
        $stmtC = $conexao->prepare($sqlCheck);
        $stmtC->bind_param("ii", $usuario_id, $anoCheck);
        $stmtC->execute();
        $resC = $stmtC->get_result();
        
        if ($resC->num_rows > 0) {
            // Existe! Pode apagar de boa.
            $stmtDelD = $conexao->prepare("DELETE FROM despesas WHERE usuario_id = ? AND YEAR(data) = ?");
            $stmtDelD->bind_param("ii", $usuario_id, $anoCheck);
            $stmtDelD->execute();
            
            $stmtDelR = $conexao->prepare("DELETE FROM rendas WHERE usuario_id = ? AND YEAR(data) = ?");
            $stmtDelR->bind_param("ii", $usuario_id, $anoCheck);
            $stmtDelR->execute();
        }
        $stmtC->close();
    }
    $stmtA->close();
}

/**
 * Função principal a ser chamada no login quando virar o ano.
 * Ela vai iterar pelos anos perdidos e gerar relatórios, migrar, limpar.
 */
function processarViradaMultiplosAnos($usuario_id, $anoUltima, $anoAtual) {
    global $conexao;

    // Para cada ano que passou, gerar snapshot (se não existir)
    for ($a = $anoUltima; $a < $anoAtual; $a++) {
        // Verifica se já gerou
        $sqlCheck = "SELECT id FROM relatorios_anuais WHERE usuario_id = ? AND ano = ?";
        $stmtC = $conexao->prepare($sqlCheck);
        $stmtC->bind_param("ii", $usuario_id, $a);
        $stmtC->execute();
        $resC = $stmtC->get_result();
        $jaGerou = $resC->num_rows > 0;
        $stmtC->close();

        if (!$jaGerou) {
            $sucessoSnap = gerarRelatorioAnual($usuario_id, $a);
        }
    }

    // A migração de recorrentes só precisa acontecer do último ano ativo ($anoUltima) 
    // para o ano atual. O resto do histórico não precisa de recorrentes "passando reto"
    // ou se precisasse, a lógica é complexa. Assumimos a migração do anoPassado para o anoAtual.
    $anoPassado = $anoAtual - 1;
    migrarRecorrentesParaAnoAtual($usuario_id, $anoPassado, $anoAtual, date('n'));

    // Limpeza segura
    limparDadosAntigosSeguro($usuario_id);
}
