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

// função para limpar dados antigos
function limparDadosAntigos(int $userId): bool
{
    global $conexao;

    $anoAtual = date('Y');

    $conexao->begin_transaction();

    try {
        // Deleta despesas antigas
        $sqlDespesas = "DELETE FROM despesas WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtDespesas = $conexao->prepare($sqlDespesas);
        $stmtDespesas->bind_param("ii", $userId, $anoAtual);
        $stmtDespesas->execute();
        $stmtDespesas->close();

        // Deleta rendas antigas
        $sqlRendas = "DELETE FROM rendas WHERE usuario_id = ? AND YEAR(data) < ?";
        $stmtRendas = $conexao->prepare($sqlRendas);
        $stmtRendas->bind_param("ii", $userId, $anoAtual);
        $stmtRendas->execute();
        $stmtRendas->close();

        $conexao->commit();
        return true;

    } catch (Exception $e) {
        $conexao->rollback();
        return false;
    }
}

try {
    // limpa os dados do ano anterior.
    $limpezaOk = limparDadosAntigos($_SESSION['id']);

    if (!$limpezaOk) {
        throw new Exception("Falha ao limpar os dados antigos.");
    }

    // atualiza a flag para liberar o acesso do usuário ao sistema.
    $stmt = $conexao->prepare("UPDATE usuarios SET relatorio_anual_pendente = 0 WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    unset($_SESSION['relatorio_pendente']);

    // responde ao JavaScript com sucesso.
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success', 'message' => 'Ano finalizado com sucesso.']);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro no servidor.']);
}