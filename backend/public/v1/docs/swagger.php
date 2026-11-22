<?php
ini_set('display_errors', 0);
error_reporting(0);

require __DIR__ . '/../../../vendor/autoload.php';

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base = preg_replace('#/public/v1/docs/swagger\.php$#', '', $_SERVER['SCRIPT_NAME'] ?? '/backend');
define('API_BASE_URL', $scheme . '://' . $host . $base);

$files = array_merge([__DIR__ . '/doc_setup.php'], glob(__DIR__ . '/paths/*.php'));
foreach ($files as $file) {
    require_once $file;
}

$openapi = (new \OpenApi\Generator())->generate($files);

header('Content-Type: application/json');
echo $openapi->toJson();
