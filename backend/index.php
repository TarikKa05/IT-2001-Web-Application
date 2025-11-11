<?php
require '../vendor/autoload.php'; //run autoloader

require_once __DIR__ . '/rest/services/CategoryService.php';
Flight::register('categoryService', 'CategoryService'); 

require_once __DIR__ . '/rest/services/OrderService.php';
Flight::register('orderService', 'OrderService');

require_once __DIR__ . '/rest/services/OrderProductService.php';
Flight::register('orderProductService', 'OrderProductService');

require_once __DIR__ . '/rest/routes/OrderRoutes.php';
require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/OrderProductRoutes.php';
Flight::start();



?>
