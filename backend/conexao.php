<?php
require_once 'funcoes.php';

// Carrega o .env
carregarEnv(__DIR__ . '/../db.env');

// Detecta o host atual
$hostAtual = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Configuração de conexão
if ($hostAtual == 'localhost') {
    // Ambiente local
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'ecoflow';
} else {
    // Ambiente de produção
    $host = $_ENV['DB_HOST'] ?? '';
    $username = $_ENV['DB_USER'] ?? '';
    $password = $_ENV['DB_PASS'] ?? '';
    $dbname = $_ENV['DB_NAME'] ?? '';
}

// Conexão MySQL
$conexao = new mysqli($host, $username, $password, $dbname);

if ($conexao->connect_error) {
    die("Erro na conexão! " . $conexao->connect_error);
}