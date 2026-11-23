<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path !== '/' && is_file(__DIR__ . $path)) {
    return false;
}

if ($path !== '/' && is_dir(__DIR__ . $path) && (strpos($path, '/backend') !== 0 || strpos($path, '/backend/public/') === 0)) {
    return false;
}

if (strpos($path, '/backend') === 0) {
    $_SERVER['SCRIPT_NAME'] = '/backend/index.php';
    chdir(__DIR__ . '/backend');
    require __DIR__ . '/backend/index.php';
    return true;
}

header('Location: /frontend/');
