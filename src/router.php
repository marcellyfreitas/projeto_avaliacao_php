<?php

$root = dirname(__FILE__);
$uri  = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$path = ltrim($path, '/');

$filePath = $root . '/' . $path;
if ($path !== '' && file_exists($filePath) && !is_dir($filePath)) {
    return false;
}

if ($path === '' || $path === 'index.php') {
    header('Location: /login');
    exit;
}

if ($path === 'login') {
    $_GET['route'] = 'login';
} elseif ($path === 'logout') {
    $_GET['route'] = 'logout';
} elseif ($path === 'dashboard') {
    $_GET['route'] = 'dashboard';
} elseif ($path === 'servico/novo') {
    $_GET['route'] = 'service_new';
} elseif (preg_match('#^servico/(\d+)/editar$#', $path, $m)) {
    $_GET['route'] = 'service_edit';
    $_GET['id']    = $m[1];
} elseif (preg_match('#^servico/(\d+)/excluir$#', $path, $m)) {
    $_GET['route'] = 'service_delete';
    $_GET['id']    = $m[1];
} elseif (preg_match('#^servico/(\d+)/finalizar$#', $path, $m)) {
    $_GET['route'] = 'service_finish';
    $_GET['id']    = $m[1];
} else {
    $_GET['route'] = $path;
}

require $root . '/index.php';
