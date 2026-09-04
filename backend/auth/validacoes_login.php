<?php
function logMsg($msg)
{
    // Salva o log num arquivo 'debug_recorrentes.log' com data e hora
    $file = __DIR__ . '/debug_recorrentes.log';
    $time = date('d/m/Y H:i:s');
    file_put_contents($file, "[$time] $msg" . PHP_EOL, FILE_APPEND);
}

function processarVerificacoesLogin(int $id, int $cargo, ?string $ultima_verificacao_db): void
{
    global $conexao;
    if ($cargo == 0 || $cargo == 1) {
        $hoje = new DateTime();
        $ultimaVerificacao = new DateTime($ultima_verificacao_db ?? '1970-01-01');

        // PRIMEIRO: Atualiza a data da última verificação para "AGORA"
        $stmtUpdateData = $conexao->prepare("UPDATE usuarios SET ultima_verificacao = NOW() WHERE id = ?");
        $stmtUpdateData->bind_param("i", $id);
        $stmtUpdateData->execute();
        $stmtUpdateData->close();

        // 1. VERIFICAÇÃO DE INÍCIO DE ANO (Relatório Anual e Limpeza)
        if ($ultimaVerificacao->format('Y') < $hoje->format('Y')) {
            require_once __DIR__ . "/../relatorio/gerar_snapshot.php";
            processarViradaMultiplosAnos($id, (int)$ultimaVerificacao->format('Y'), (int)$hoje->format('Y'));
        }

        // 2. VERIFICAÇÃO DE RECORRENTES (Despesas/Rendas)
        if ($ultimaVerificacao->format('Y-m') < $hoje->format('Y-m')) {
            verificarRecorrentes($id, $ultimaVerificacao->format('Y-m-d'));
        }
    }
}

function verificarRecorrentes(int $userId, string $ultimaVerificacao = null): void
{
    require_once __DIR__ . '/../recorrentes/materializar.php';
    if (!$ultimaVerificacao) {
        $ultimaVerificacao = date('Y-m-01', strtotime('-1 month'));
    }
    materializarParaPeriodo($userId, $ultimaVerificacao, date('Y-m-d'));
}
