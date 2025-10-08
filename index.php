<?php
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

// rotas
$routes = [
    '' => 'pages/landing.php',

    // autenticação
    'login' => 'pages/login_form.php',
    'fazer_login' => 'backend/auth/login.php',
    'cadastro' => 'pages/cadastro_form.php',
    'fazer_cadastro' => 'backend/auth/cadastro.php',

    // paginas do usuario
    'dashboard' => 'pages/dashboard.php',
    'rendas' => 'pages/rendas.php',
    'despesas' => 'pages/despesas.php',
    'perfil' => 'pages/perfil.php',
];

if (array_key_exists($url, $routes)) {
    require $routes[$url];
    exit;
}

http_response_code(404);
require 'erro404.php';