<?php
require_once __DIR__ . '/../valida.php';

header('Content-Type: application/json; charset=utf-8');

$userId = $_SESSION['id'];

// Listar templates de despesas
$sqlDespesas = "SELECT r.*, c.nome_categoria 
                FROM recorrentes r 
                LEFT JOIN categorias c ON r.categoria_id = c.id 
                WHERE r.usuario_id = ? AND r.tipo = 'despesa'
                ORDER BY r.ativo DESC, r.dia_vencimento ASC";
$stmtD = $conexao->prepare($sqlDespesas);
$stmtD->bind_param("i", $userId);
$stmtD->execute();
$resD = $stmtD->get_result();
$despesas = [];
$totalAtivoDespesas = 0;
while ($row = $resD->fetch_assoc()) {
    $despesas[] = $row;
    if ($row['ativo'] == 1) {
        $totalAtivoDespesas += $row['valor'];
    }
}
$stmtD->close();

// Listar templates de rendas
$sqlRendas = "SELECT * FROM recorrentes 
              WHERE usuario_id = ? AND tipo = 'renda'
              ORDER BY ativo DESC, dia_vencimento ASC";
$stmtR = $conexao->prepare($sqlRendas);
$stmtR->bind_param("i", $userId);
$stmtR->execute();
$resR = $stmtR->get_result();
$rendas = [];
$totalAtivoRendas = 0;
while ($row = $resR->fetch_assoc()) {
    $rendas[] = $row;
    if ($row['ativo'] == 1) {
        $totalAtivoRendas += $row['valor'];
    }
}
$stmtR->close();

responderJSON(true, 'Recorrências listadas com sucesso', [
    'despesas' => $despesas,
    'rendas' => $rendas,
    'total_recorrentes_despesas' => $totalAtivoDespesas,
    'total_recorrentes_rendas' => $totalAtivoRendas,
    'total_recorrentes' => $totalAtivoDespesas + $totalAtivoRendas
]);
