<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once("../../backend/config/database.php");
require_once("../../backend/includes/valida.php");

function gerarRelatorioAnual($conn, $user_id)
{
    $anoAtual = date('Y');
    $html = "<h1 style='text-align: center;'>Relatório Anual - $anoAtual</h1>";
    $html .= "<p style='text-align: center;'>Resumo das atividades financeiras do ano.</p>";

    // Rendas
    $html .= "<h2>Rendas</h2>";
    $queryRendas = "SELECT descricao, valor, data FROM rendas WHERE user_id = ? AND YEAR(data) = ?";
    $stmtRendas = $conn->prepare($queryRendas);
    $stmtRendas->bind_param("ii", $user_id, $anoAtual);
    $stmtRendas->execute();
    $resultRendas = $stmtRendas->get_result();

    if ($resultRendas->num_rows > 0) {
        $html .= "<table border='1' style='width:100%; border-collapse:collapse;'>";
        $html .= "<thead><tr><th>Descrição</th><th>Valor</th><th>Data</th></tr></thead><tbody>";
        while ($row = $resultRendas->fetch_assoc()) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($row['descricao']) . "</td>";
            $html .= "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
            $html .= "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";
    } else {
        $html .= "<p>Não foram encontradas rendas para o ano de $anoAtual.</p>";
    }

    // Despesas
    $html .= "<h2>Despesas</h2>";
    $queryDespesas = "SELECT descricao, valor, data, status FROM despesas WHERE user_id = ? AND YEAR(data) = ?";
    $stmtDespesas = $conn->prepare($queryDespesas);
    $stmtDespesas->bind_param("ii", $user_id, $anoAtual);
    $stmtDespesas->execute();
    $resultDespesas = $stmtDespesas->get_result();

    if ($resultDespesas->num_rows > 0) {
        $html .= "<table border='1' style='width:100%; border-collapse:collapse;'>";
        $html .= "<thead><tr><th>Descrição</th><th>Valor</th><th>Data</th><th>Status</th></tr></thead><tbody>";
        while ($row = $resultDespesas->fetch_assoc()) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($row['descricao']) . "</td>";
            $html .= "<td>R$ " . number_format($row['valor'], 2, ',', '.') . "</td>";
            $html .= "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['status']) . "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";
    } else {
        $html .= "<p>Não foram encontradas despesas para o ano de $anoAtual.</p>";
    }

    // Investimentos
    $html .= "<h2>Investimentos</h2>";
    $queryInvestimentos = "SELECT nome, custo, tipo, data FROM investimentos WHERE user_id = ? AND YEAR(data) = ?";
    $stmtInvestimentos = $conn->prepare($queryInvestimentos);
    $stmtInvestimentos->bind_param("ii", $user_id, $anoAtual);
    $stmtInvestimentos->execute();
    $resultInvestimentos = $stmtInvestimentos->get_result();

    if ($resultInvestimentos->num_rows > 0) {
        $html .= "<table border='1' style='width:100%; border-collapse:collapse;'>";
        $html .= "<thead><tr><th>Nome</th><th>Custo</th><th>Tipo</th><th>Data</th></tr></thead><tbody>";
        while ($row = $resultInvestimentos->fetch_assoc()) {
            $html .= "<tr>";
            $html .= "<td>" . htmlspecialchars($row['nome']) . "</td>";
            $html .= "<td>R$ " . number_format($row['custo'], 2, ',', '.') . "</td>";
            $html .= "<td>" . htmlspecialchars($row['tipo']) . "</td>";
            $html .= "<td>" . htmlspecialchars(date('d/m/Y', strtotime($row['data']))) . "</td>";
            $html .= "</tr>";
        }
        $html .= "</tbody></table>";
    } else {
        $html .= "<p>Não foram encontrados investimentos para o ano de $anoAtual.</p>";
    }

    $stmtRendas->close();
    $stmtDespesas->close();
    $stmtInvestimentos->close();

    return $html;
}

if (isset($_GET['rel_anual']) && $_GET['rel_anual'] === 'true') {
    try {
        $user_id = $_SESSION['id'];
        $relatorio = gerarRelatorioAnual($conn, $user_id);

        if (!empty($relatorio)) {
            // Gera o PDF
            $pdf = new \Mpdf\Mpdf();
            $pdf->setTitle("Relatório Anual " . date('Y'));
            $pdf->WriteHTML($relatorio);

            $pdf->output("Relatorio_Anual_" . date('Y') . ".pdf", "D");
            exit;

            $_SESSION['rel_anual'] = true;
        } else {
            echo "Erro: relatório vazio.";
        }
    } catch (Exception $e) {
        error_log("Erro ao gerar PDF: " . $e->getMessage());
    }
}
