<?php
require_once __DIR__ . '/../valida.php';

// Define o cabeçalho como JSON
header('Content-Type: application/json');

// padronizar resposta
function responder_json($sucesso, $mensagem)
{
    echo json_encode(['success' => $sucesso, 'message' => $mensagem]);
    exit;
}

$usuario_id = $_SESSION['id'];
$dados = json_decode(file_get_contents('php://input'), true);

// valida ID
if (!$dados || !isset($dados['id']) || !is_numeric($dados['id'])) {
    responder_json(false, 'ID do aviso inválido.');
}
$aviso_id = (int) $dados['id'];

// sql
$sql = "INSERT INTO usuarios_avisos_vistos (usuario_id, aviso_id) 
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE data_visto = NOW()";

$stmt = $conexao->prepare($sql);

if ($stmt === false) {
    // Erro na preparação da query
    responder_json(false, 'Erro no servidor (prepare): ' . $conexao->error);
}
$stmt->bind_param("ii", $usuario_id, $aviso_id);

// Tenta executar
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        responder_json(true, 'Aviso marcado como visto.');
    } else {
        // affected_rows = 0 pode significar que o ON DUPLICATE KEY UPDATE rodou
        responder_json(true, 'Aviso já estava marcado como visto.');
    }
} else {
    // Erro na execução
    responder_json(false, 'Erro no servidor (execute): ' . $stmt->error);
}

$stmt->close();