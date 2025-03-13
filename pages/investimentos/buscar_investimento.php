<?php
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
if (!$input || !isset($input["tipo"]) || !isset($input["nome"])) {
    echo json_encode(["erro" => "Dados inválidos"]);
    exit;
}

$tipo = $input["tipo"];
$nome = $input["nome"];

include_once "../../backend/config/loadEnv.php";

loadEnv("../../senhas.env");

$senha_api = $_ENV['SENHA_API'];

$headers = [
    "Authorization: Bearer " . $senha_api,
    "Content-Type: application/json",
];

$link = "https://api.deepseek.com/v1/chat/completions";
$modelo = "deepseek-chat";

$pergunta = "
Você é um assistente financeiro. Responda sucintamente com os dados sobre o ativo solicitado.

Ativo:
- Nome ou ticker: $nome
- Tipo: $tipo

Formato da resposta (sem explicações):";
if ($tipo == "acao") {
    $pergunta .= "
    {
        \"nome\": \"$nome\",
        \"ticker\": \"TICKER\",
        \"empresa\": \"EMPRESA\",
        \"valor\": \"R$ 00,00\",
        \"rendimento\": \"X% ao ano\",
        \"recorrencia\": \"Mensal/Trimestral/Anual\"
    }
    ";
} elseif ($tipo == "fii") {
    $pergunta .= "
    {
        \"nome\": \"$nome\",
        \"ticker\": \"TICKER\",
        \"emissor\": \"EMISSOR\",
        \"valor\": \"R$ 00,00\",
        \"rendimento\": \"X% ao ano\",
        \"recorrencia\": \"Mensal/Trimestral/Anual\"
    }
    ";
} elseif ($tipo == "rendafixa") {
    $pergunta .= "
    {
        \"nome\": \"$nome\",
        \"emissor\": \"EMISSOR\",
        \"valor\": \"R$ 00,00\",
        \"rendimento\": \"X% ao ano\",
        \"vencimento\": \"DATA\"
    }
    ";
} else {
    echo json_encode(["erro" => "Tipo de investimento inválido"]);
    exit;
}

$body_mensagem = [
    "model" => $modelo,
    "messages" => [
        [
            "role" => "user",
            "content" => $pergunta,
        ]
    ]
];

$requisicao = curl_init($link);
curl_setopt($requisicao, CURLOPT_HTTPHEADER, $headers);
curl_setopt($requisicao, CURLOPT_POST, 1);
curl_setopt($requisicao, CURLOPT_POSTFIELDS, json_encode($body_mensagem));
curl_setopt($requisicao, CURLOPT_RETURNTRANSFER, true);

$resposta = curl_exec($requisicao);

$http_code = curl_getinfo($requisicao, CURLINFO_HTTP_CODE);
if ($http_code != 200) {
    die("Erro HTTP $http_code: " . $resposta);
}

curl_close($requisicao);

$mensagem = json_decode($resposta);

if (isset($mensagem->choices[0]->message->content)) {
    $resposta_json = json_decode($mensagem->choices[0]->message->content, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        $resultado = [
            "nome" => $resposta_json["nome"] ?? "Desconhecido",
            "ticker" => $resposta_json["ticker"] ?? "Desconhecido",
            "valor" => $resposta_json["valor"] ?? "Desconhecido",
            "rendimento" => $resposta_json["rendimento"] ?? "Desconhecido"
        ];

        if ($tipo == "acao") {
            $resultado["empresa"] = $resposta_json["empresa"] ?? "Desconhecido";
            $resultado["recorrencia"] = $resposta_json["recorrencia"] ?? "Desconhecido";
        } elseif ($tipo == "fii") {
            $resultado["emissor"] = $resposta_json["emissor"] ?? "Desconhecido";
            $resultado["recorrencia"] = $resposta_json["recorrencia"] ?? "Desconhecido";
        } elseif ($tipo == "rendafixa") {
            $resultado["emissor"] = $resposta_json["emissor"] ?? "Desconhecido";
            $resultado["vencimento"] = $resposta_json["vencimento"] ?? "Desconhecido";
        }

        echo json_encode($resultado);
    } else {
        echo json_encode(["erro" => "Erro ao processar a resposta da API."]);
    }
} else {
    echo json_encode(["erro" => "Resposta inesperada da API."]);
}