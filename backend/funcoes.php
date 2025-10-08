<?php
// Função para carregar variáveis do .env
function carregarEnv($caminho)
{
    if (!file_exists($caminho)) {
        throw new Exception("Arquivo .env não encontrado em: " . $caminho);
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        // Ignorar comentários
        if (strpos(trim($linha), '#') === 0) {
            continue;
        }

        // Separar chave e valor
        list($chave, $valor) = explode('=', $linha, 2);

        $chave = trim($chave);
        $valor = trim($valor, " \"'"); // já remove espaços e aspas

        // Salvar no ambiente
        putenv("$chave=$valor");
        $_ENV[$chave] = $valor;
    }
}

// função para definir o BASE_URL
if (!defined('BASE_URL')) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        define('BASE_URL', '/ecoflow/');
    } else {
        define('BASE_URL', '/');
    }
}

function gerarCSRF()
{
    $_SESSION["csrf"] = (isset($_SESSION["csrf"])) ? $_SESSION["csrf"] : hash('sha256', random_bytes(32));

    return ($_SESSION["csrf"]);
}

function validarCSRF($csrf)
{
    if (!isset($_SESSION["csrf"])) {
        return (false);
    }
    if ($_SESSION["csrf"] !== $csrf) {
        return false;
    }
    if (!hash_equals($_SESSION["csrf"], $csrf)) {
        return false;
    }

    return true;
}

function formatarReais(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarDataHora(string $dataHora, bool $mostrarHora = true): string
{
    $timestamp = strtotime($dataHora);

    if ($mostrarHora) {
        return date('d/m/Y H:i', $timestamp); // Ex: 08/10/2025 14:35
    } else {
        return date('d/m/Y', $timestamp);     // Ex: 08/10/2025
    }
}

function formatarData(string $data): string
{
    $timestamp = strtotime($data);
    return date('d/m/Y', $timestamp); // Ex: 08/10/2025
}

function tipoCategorias($categoria)
{
    if ($categoria > 0 && $categoria < 7) {
        switch ($categoria) {
            case 1:
                return 'Moradia';
            case 2:
                return 'Alimentação';
            case 3:
                return 'Transporte';
            case 4:
                return 'Saúde';
            case 5:
                return 'Educação';
            case 6:
                return 'Lazer';
        }
    } else {
        return 'Outro';
    }
}
function validarDescricao(string $descricao) {
    // Remove espaços extras
    $descricao = trim($descricao);

    // Regex: apenas letras, números, espaços, parênteses e dois pontos
    if (!preg_match('/^[A-Za-zÀ-ú0-9\s():]+$/u', $descricao)) {
        return false;
    }

    // Normaliza capitalização: primeira letra maiúscula e o resto minúsculo
    $descricao = mb_strtolower($descricao, 'UTF-8');
    $descricao = mb_convert_case($descricao, MB_CASE_TITLE, 'UTF-8');

    return $descricao;
}
function validarValor($valor) {
    // Substitui vírgula por ponto, se houver
    $valor = str_replace(',', '.', $valor);

    // Verifica se é número decimal positivo
    if (!is_numeric($valor)) {
        return false;
    }

    return (float)$valor;
}


?>