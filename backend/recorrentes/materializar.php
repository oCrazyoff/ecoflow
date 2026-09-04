<?php
/**
 * Materialização de recorrências.
 * Este arquivo deve ser incluído por scripts que já possuem $conexao disponível.
 */

/**
 * Materializa (cria) as entradas de despesas/rendas recorrentes para um determinado mês/ano.
 * 
 * @param int $userId O ID do usuário
 * @param int $mes O mês desejado
 * @param int $ano O ano desejado
 */
function materializarRecorrentes(int $userId, int $mes, int $ano): void {
    global $conexao;
    
    // Pegar último e primeiro dia do mês desejado
    $primeiroDia = sprintf('%04d-%02d-01', $ano, $mes);
    $ultimoDia = date('Y-m-t', strtotime($primeiroDia));
    
    // 1. Buscar todos os templates ativos para o usuário neste período
    $sql = "SELECT * FROM recorrentes 
            WHERE usuario_id = ? 
              AND ativo = 1 
              AND data_inicio <= ? 
              AND (data_fim IS NULL OR data_fim >= ?)";
              
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("iss", $userId, $ultimoDia, $primeiroDia);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $templates = [];
    while ($row = $result->fetch_assoc()) {
        $templates[] = $row;
    }
    $stmt->close();
    
    foreach ($templates as $template) {
        $recorrenteId = $template['id'];
        $tipo = $template['tipo']; // 'renda' ou 'despesa'
        $dia = $template['dia_vencimento'];
        
        // a. Calcular dia correto (se o mês tem menos dias que o dia_vencimento)
        $diasNoMes = date('t', strtotime($primeiroDia));
        $diaReal = min($dia, $diasNoMes);
        $dataReal = sprintf('%04d-%02d-%02d', $ano, $mes, $diaReal);
        
        if ($tipo === 'despesa') {
            // b. Verifica se já existe em despesas
            $chk = $conexao->prepare("SELECT id FROM despesas WHERE recorrente_id = ? AND MONTH(data) = ? AND YEAR(data) = ?");
            $chk->bind_param("iii", $recorrenteId, $mes, $ano);
            $chk->execute();
            $exists = $chk->get_result()->num_rows > 0;
            $chk->close();
            
            if (!$exists) {
                // Insere nova despesa
                $ins = $conexao->prepare("INSERT INTO despesas (usuario_id, categoria_id, descricao, valor, data, status, tipo, recorrente, recorrente_id) VALUES (?, ?, ?, ?, ?, 0, 0, 1, ?)");
                $ins->bind_param("iisssi", $userId, $template['categoria_id'], $template['descricao'], $template['valor'], $dataReal, $recorrenteId);
                $ins->execute();
                $ins->close();
            }
        } else {
            // c. Verifica se já existe em rendas
            $chk = $conexao->prepare("SELECT id FROM rendas WHERE recorrente_id = ? AND MONTH(data) = ? AND YEAR(data) = ?");
            $chk->bind_param("iii", $recorrenteId, $mes, $ano);
            $chk->execute();
            $exists = $chk->get_result()->num_rows > 0;
            $chk->close();
            
            if (!$exists) {
                // Insere nova renda
                $ins = $conexao->prepare("INSERT INTO rendas (usuario_id, descricao, valor, data, recorrente, recorrente_id) VALUES (?, ?, ?, ?, 1, ?)");
                $ins->bind_param("isssi", $userId, $template['descricao'], $template['valor'], $dataReal, $recorrenteId);
                $ins->execute();
                $ins->close();
            }
        }
    }
}

/**
 * Materializa as recorrências para um período (usado para "catch-up" no login).
 *
 * @param int $userId O ID do usuário
 * @param string $dataInicio Data inicial (Y-m-d)
 * @param string $dataFim Data final (Y-m-d)
 */
function materializarParaPeriodo(int $userId, string $dataInicio, string $dataFim): void {
    $inicio = new DateTime($dataInicio);
    $fim = new DateTime($dataFim);
    
    // Garantir que passamos por todos os meses no intervalo
    // Modificar para o primeiro dia do mês para o iterador não pular meses curtos
    $inicio->modify('first day of this month');
    $fim->modify('first day of this month');
    
    $intervalo = new DateInterval('P1M');
    $fimInclusive = clone $fim;
    $fimInclusive->modify('+1 month');
    $periodo = new DatePeriod($inicio, $intervalo, $fimInclusive);
    
    foreach ($periodo as $dt) {
        $mes = (int) $dt->format('m');
        $ano = (int) $dt->format('Y');
        materializarRecorrentes($userId, $mes, $ano);
    }
}
