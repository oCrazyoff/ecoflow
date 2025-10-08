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

    // rotas de busca
    'buscar_rendas' => 'backend/buscar/rendas.php',
    'buscar_despesas' => 'backend/buscar/despesas.php',

    // rotas de cadastro
    'cadastrar_rendas' => 'backend/cadastrar/rendas.php',
    'cadastrar_despesas' => 'backend/cadastrar/despesas.php',

    // rotas de edição
    'editar_rendas' => 'backend/editar/rendas.php',
    'editar_despesas' => 'backend/editar/despesas.php',

    // rotas de deletar
    'deletar_rendas' => 'backend/deletar/rendas.php',
    'deletar_despesas' => 'backend/deletar/despesas.php',
];

if (array_key_exists($url, $routes)) {
    require $routes[$url];
    exit;
}

http_response_code(404);
require 'erro404.php';