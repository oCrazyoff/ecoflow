<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once("../../backend/config/database.php");
require_once("../../backend/includes/valida.php");

function gerarRelatorioAnual($conn, $user_id)
{
    $ano_atual = date('Y');
    $html = "
            <style>
    body {
        background-color: #ffffff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    header {
        width: 100%;
        padding: 10px;
        background-color: #ffffff;
        border-bottom: 1px solid #ccc;
    }

    header h1 {
        font-size: 18px;
        margin: 0;
    }

    header p {
        font-size: 14px;
        margin: 0;
    }

    .cards {
        width: 100%;
        padding: 10px 0;
    }

    .card {
        display: block;
        background-color: #ffffff;
        padding: 15px;
        margin-bottom: 20px;
        width: 100%;
        border: 1px solid #cccccc;
    }

    .card h3 {
        font-size: 16px;
        color: #707070;
        margin-top: 0;
    }

    .card h4 {
        font-size: 18px;
        text-align: center;
        margin: 10px 0;
    }

    .card p {
        font-size: 12px;
        color: #707070;
        margin: 5px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-top: 10px;
    }

    table th,
    table td {
        border: 1px solid #dddddd;
        padding: 8px;
        text-align: left;
    }

    table th {
        background-color: #f0f0f0;
        color: #333333;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Estilos de status */

    .status-pago {
        background-color: #c8e6c9;
        border: 1px solid #2e7d32;
        color: #2e7d32;
        padding: 2px 6px;
        font-weight: bold;
        font-size: 12px;
        display: inline-block;
    }

    .status-pendente {
        background-color: #fff9c4;
        border: 1px solid #fbc02d;
        color: #f57f17;
        padding: 2px 6px;
        font-weight: bold;
        font-size: 12px;
        display: inline-block;
    }
</style>

            ";

    $html .= "<table>
                <tr>
                    <td>
                        <h1>Relatório Anual - $ano_atual</h1>
                        <p>Resumo das suas finanças durante o ano de $ano_atual</p>
                    </td>
                    <td>
                        <h2>EcoFlow</h2>
                    </td>
                </tr>
            </table>";

    // Renda total
    $total_rendas = 0;
    $sql_rendas = "SELECT SUM(valor) FROM rendas WHERE user_id = ? AND YEAR(data) = ?";
    $stmt_rendas = $conn->prepare($sql_rendas);
    $stmt_rendas->bind_param("is", $user_id, $ano_atual);
    $stmt_rendas->execute();
    $stmt_rendas->bind_result($total_rendas);
    $stmt_rendas->fetch();
    $stmt_rendas->close();

    $total_rendas = number_format($total_rendas, 2, ',', '.');

    // Despesa total
    $total_despesas = 0;
    $sql_despesas = "SELECT SUM(valor) FROM despesas WHERE user_id = ? AND YEAR(data) = ?";
    $stmt_despesas = $conn->prepare($sql_despesas);
    $stmt_despesas->bind_param("is", $user_id, $ano_atual);
    $stmt_despesas->bind_result($total_despesas);
    $stmt_despesas->execute();
    $stmt_despesas->fetch();
    $stmt_despesas->close();

    $total_despesas = number_format($total_despesas, 2, ',', '.');

    // Investimento total
    $total_investimentos = 0;
    $sql_investimentos = "SELECT SUM(custo) FROM investimentos WHERE user_id = ? AND YEAR(data) = ?";
    $stmt_investimentos = $conn->prepare($sql_investimentos);
    $stmt_investimentos->bind_param("is", $user_id, $ano_atual);
    $stmt_investimentos->execute();
    $stmt_investimentos->bind_result($total_investimentos);
    $stmt_investimentos->fetch();
    $stmt_investimentos->close();

    $total_investimentos = number_format($total_investimentos, 2, ',', '.');

    $html .= "
<table width='100%' cellspacing='10' cellpadding='15'>
    <tr>
        <td width='33%' style='background-color: #95d5b2; border: 1px solid #60d39470; border-radius: 8px; text-align: center;'>
            <h3 style='color: #25a18e;'>Renda Anual</h3>
            <p style='font-size: 1.5em; color: #000;'><strong>R$ $total_rendas</strong></p>
            <p style='color: #707070;'>Janeiro a Dezembro</p>
        </td>
        <td width='33%' style='background-color: #f4acb7; border: 1px solid #e5989b; border-radius: 8px; text-align: center;'>
            <h3 style='color: #d80032;'>Despesa Anual</h3>
            <p style='font-size: 1.5em; color: #000;'><strong>R$ $total_despesas</strong></p>
            <p style='color: #707070;'>Janeiro a Dezembro</p>
        </td>
        <td width='33%' style='background-color: #bde0fe42; border: 1px solid #bde0fe98; border-radius: 8px; text-align: center;'>
            <h3 style='color: #219ebc;'>Investimento Anual</h3>
            <p style='font-size: 1.5em; color: #000;'><strong>R$ $total_investimentos</strong></p>
            <p style='color: #707070;'>Janeiro a Dezembro</p>
        </td>
    </tr>
</table>
";


    // Tabelas
    $html .= "<div class='cards' id='tabelas'>";

    // Rendas
    $html .= "<div class='card'>";
    $html .= "<h3>Rendas</h3>";
    $sql_rendas = "SELECT descricao, valor, data FROM rendas WHERE user_id = ? AND YEAR(data) = ?";
    $stmt_rendas = $conn->prepare($sql_rendas);
    $stmt_rendas->bind_param("ii", $user_id, $ano_atual);
    $stmt_rendas->execute();
    $result_rendas = $stmt_rendas->get_result();

    $rendas_por_mes = [];

    while ($row = $result_rendas->fetch_assoc()) {
        $mes = date('m', strtotime($row['data']));
        $rendas_por_mes[$mes][] = $row;
    }

    if (!empty($rendas_por_mes)) {
        foreach ($rendas_por_mes as $mes => $rendas) {
            setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');
            $nome_mes = ucfirst(strftime('%B', mktime(0, 0, 0, $mes, 1)));
            $html .= "<h4>$nome_mes</h4>";
            $html .= "<table>";
            $html .= "<thead><tr><th>Descrição</th><th>Valor</th><th>Data</th></tr></thead><tbody>";
            foreach ($rendas as $renda) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($renda['descricao']) . "</td>";
                $html .= "<td>R$ " . number_format($renda['valor'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars(date('d/m/Y', strtotime($renda['data']))) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody></table>";
        }
    } else {
        $html .= "<p>Não foram encontradas rendas para o ano de $ano_atual.</p>";
    }

    $html .= "</div>";

    // Despesas
    $html .= "<div class='card'>";
    $html .= "<h3>Despesas</h3>";

    $sql_despesas = "SELECT descricao, valor, data, status FROM despesas WHERE user_id = ? AND YEAR(data) = ?";
    $stmt_despesas = $conn->prepare($sql_despesas);
    $stmt_despesas->bind_param("ii", $user_id, $ano_atual);
    $stmt_despesas->execute();
    $result_despesas = $stmt_despesas->get_result();

    $despesas_por_mes = [];

    while ($row = $result_despesas->fetch_assoc()) {
        $mes = date('m', strtotime($row['data']));
        $despesas_por_mes[$mes][] = $row;
    }

    if (!empty($despesas_por_mes)) {
        foreach ($despesas_por_mes as $mes => $despesas) {
            setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');
            $nome_mes = ucfirst(strftime('%B', mktime(0, 0, 0, $mes, 1)));
            $html .= "<h4>$nome_mes</h4>";
            $html .= "<table>";
            $html .= "<thead><tr><th>Descrição</th><th>Valor</th><th>Data</th><th>Status</th></tr></thead><tbody>";
            foreach ($despesas as $despesa) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($despesa['descricao']) . "</td>";
                $html .= "<td>R$ " . number_format($despesa['valor'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars(date('d/m/Y', strtotime($despesa['data']))) . "</td>";
                $html .= "<td> <p id='" . (($despesa['status'] == 'Pago') ? 'pago' : 'pendente') . "'>" . htmlspecialchars($despesa['status']) . "</p></td>";
                $html .= "</tr>";
            }
            $html .= "</tbody></table>";
        }
    } else {
        $html .= "<p>Não foram encontradas despesas para o ano de $ano_atual.</p>";
    }

    $html .= "</div>";


    // Investimentos
    $html .= "<div class='card'>";
    $html .= "<h3>Investimentos</h3>";

    $sql_investimentos = "SELECT nome, custo, tipo, data FROM investimentos WHERE user_id = ? AND YEAR(data) = ?";
    $stmt_investimentos = $conn->prepare($sql_investimentos);
    $stmt_investimentos->bind_param("ii", $user_id, $ano_atual);
    $stmt_investimentos->execute();
    $result_investimentos = $stmt_investimentos->get_result();

    $investimentos_por_mes = [];

    while ($row = $result_investimentos->fetch_assoc()) {
        $mes = date('m', strtotime($row['data']));
        $investimentos_por_mes[$mes][] = $row;
    }

    if (!empty($investimentos_por_mes)) {
        foreach ($investimentos_por_mes as $mes => $investimentos) {
            setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');
            $nome_mes = ucfirst(strftime('%B', mktime(0, 0, 0, $mes, 1)));
            $html .= "<h4>$nome_mes</h4>";
            $html .= "<table>";
            $html .= "<thead><tr><th>Nome</th><th>Custo</th><th>Tipo</th><th>Data</th></tr></thead><tbody>";
            foreach ($investimentos as $investimento) {
                $html .= "<tr>";
                $html .= "<td>" . htmlspecialchars($investimento['nome']) . "</td>";
                $html .= "<td>R$ " . number_format($investimento['custo'], 2, ',', '.') . "</td>";
                $html .= "<td>" . htmlspecialchars($investimento['tipo']) . "</td>";
                $html .= "<td>" . htmlspecialchars(date('d/m/Y', strtotime($investimento['data']))) . "</td>";
                $html .= "</tr>";
            }
            $html .= "</tbody></table>";
        }
    } else {
        $html .= "<p>Não foram encontrados investimentos para o ano de $ano_atual.</p>";
    }

    $html .= "</div>";


    $stmt_rendas->close();
    $stmt_despesas->close();
    $stmt_investimentos->close();

    $html .= "</div>";

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
