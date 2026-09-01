<?php
require_once __DIR__ . '/../valida.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);

$tipo = $input['tipo'] ?? null;
$ids = $input['ids'] ?? [];
$mes_destino = filter_var($input['mes_destino'] ?? null, FILTER_VALIDATE_INT);

if (!$tipo || !in_array($tipo, ['despesas', 'rendas']) || empty($ids) || !$mes_destino || $mes_destino < 1 || $mes_destino > 12) {
    echo json_encode(['sucesso' => false, 'erro' => 'Parâmetros inválidos.']);
    exit;
}

$usuario_id = $_SESSION['id'];
$criados = 0;

$conexao->begin_transaction();

try {
    foreach ($ids as $id) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if (!$id) continue;
        
        if ($tipo === 'despesas') {
            $stmt = $conexao->prepare("SELECT descricao, valor, categoria_id, data FROM despesas WHERE id = ? AND usuario_id = ? AND recorrente = 1");
            $stmt->bind_param("ii", $id, $usuario_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) {
                $stmt->close();
                continue;
            }
            $orig = $res->fetch_assoc();
            $stmt->close();
            
            $original_date = new DateTime($orig['data']);
            $dia = (int)$original_date->format('d');
            $mes_origem = (int)$original_date->format('m');
            $ano_destino = (int)$original_date->format('Y');
            
            if ($mes_destino < $mes_origem) {
                $ano_destino++;
            }
            
            $nova_data = new DateTime();
            $nova_data->setDate($ano_destino, $mes_destino, 1);
            $ultimo_dia_mes = (int)$nova_data->format('t');
            if ($dia > $ultimo_dia_mes) {
                $dia = $ultimo_dia_mes;
            }
            $nova_data->setDate($ano_destino, $mes_destino, $dia);
            $data_formatada = $nova_data->format('Y-m-d');
            
            $stmt_check = $conexao->prepare("SELECT id FROM despesas WHERE usuario_id = ? AND descricao = ? AND MONTH(data) = ? AND YEAR(data) = ?");
            $stmt_check->bind_param("isii", $usuario_id, $orig['descricao'], $mes_destino, $ano_destino);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();
            $existe = $res_check->num_rows > 0;
            $stmt_check->close();
            
            if (!$existe) {
                $status = 0;
                $tipo_despesa = 0;
                $recorrente = 1;
                $stmt_insert = $conexao->prepare("INSERT INTO despesas (usuario_id, categoria_id, descricao, valor, data, status, tipo, recorrente) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("iisdsiii", $usuario_id, $orig['categoria_id'], $orig['descricao'], $orig['valor'], $data_formatada, $status, $tipo_despesa, $recorrente);
                if ($stmt_insert->execute()) {
                    $criados++;
                }
                $stmt_insert->close();
            }
            
        } else if ($tipo === 'rendas') {
            $stmt = $conexao->prepare("SELECT descricao, valor, data FROM rendas WHERE id = ? AND usuario_id = ? AND recorrente = 1");
            $stmt->bind_param("ii", $id, $usuario_id);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) {
                $stmt->close();
                continue;
            }
            $orig = $res->fetch_assoc();
            $stmt->close();
            
            $original_date = new DateTime($orig['data']);
            $dia = (int)$original_date->format('d');
            $mes_origem = (int)$original_date->format('m');
            $ano_destino = (int)$original_date->format('Y');
            
            if ($mes_destino < $mes_origem) {
                $ano_destino++;
            }
            
            $nova_data = new DateTime();
            $nova_data->setDate($ano_destino, $mes_destino, 1);
            $ultimo_dia_mes = (int)$nova_data->format('t');
            if ($dia > $ultimo_dia_mes) {
                $dia = $ultimo_dia_mes;
            }
            $nova_data->setDate($ano_destino, $mes_destino, $dia);
            $data_formatada = $nova_data->format('Y-m-d');
            
            $stmt_check = $conexao->prepare("SELECT id FROM rendas WHERE usuario_id = ? AND descricao = ? AND MONTH(data) = ? AND YEAR(data) = ?");
            $stmt_check->bind_param("isii", $usuario_id, $orig['descricao'], $mes_destino, $ano_destino);
            $stmt_check->execute();
            $res_check = $stmt_check->get_result();
            $existe = $res_check->num_rows > 0;
            $stmt_check->close();
            
            if (!$existe) {
                $recorrente = 1;
                $stmt_insert = $conexao->prepare("INSERT INTO rendas (usuario_id, descricao, valor, data, recorrente) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("isdsi", $usuario_id, $orig['descricao'], $orig['valor'], $data_formatada, $recorrente);
                if ($stmt_insert->execute()) {
                    $criados++;
                }
                $stmt_insert->close();
            }
        }
    }
    
    $conexao->commit();
    
    if (function_exists('limparInsightsCache')) {
        limparInsightsCache();
    }
    
    echo json_encode([
        'sucesso' => true, 
        'mensagem' => "$criados itens adiantados com sucesso!", 
        'criados' => $criados
    ]);
    
} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao processar adiantamento: ' . $e->getMessage()]);
}
