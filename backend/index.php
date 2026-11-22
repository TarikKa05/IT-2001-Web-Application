<?php
require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/rest/services/UserService.php';
require_once __DIR__ . '/rest/services/CategoryService.php';
require_once __DIR__ . '/rest/services/ProductService.php';
require_once __DIR__ . '/rest/services/ProductCategoryService.php';
require_once __DIR__ . '/rest/services/OrderService.php';
require_once __DIR__ . '/rest/services/OrderProductService.php';
require_once __DIR__ . '/rest/services/WeatherService.php';

Flight::register('user_service', 'UserService');
Flight::register('category_service', 'CategoryService');
Flight::register('product_service', 'ProductService');
Flight::register('product_category_service', 'ProductCategoryService');
Flight::register('order_service', 'OrderService');
Flight::register('order_product_service', 'OrderProductService');
Flight::register('weather_service', 'WeatherService');

Flight::map('error', function (Throwable $e) {
    if ($e instanceof HttpException) {
        $status = $e->get_status();
        $message = $e->getMessage();
    } else {
        $status = 500;
        $message = 'Something went wrong on the server. Please try again later.';
        error_log($e);
    }

    Flight::response()
        ->clearBody()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['status' => $status, 'error' => $message]))
        ->send();
});

Flight::map('notFound', function () {
    $method = Flight::request()->method;
    $url = strtok(Flight::request()->url, '?');

    Flight::response()
        ->clearBody()
        ->status(404)
        ->header('Content-Type', 'application/json')
        ->write(json_encode(['status' => 404, 'error' => "Endpoint $method $url does not exist."]))
        ->send();
});

Flight::route('GET /', function () {
    Flight::json(['app' => 'Gym Club API', 'message' => 'The backend is running.']);
});

require_once __DIR__ . '/rest/routes/UserRoutes.php';
require_once __DIR__ . '/rest/routes/CategoryRoutes.php';
require_once __DIR__ . '/rest/routes/ProductRoutes.php';
require_once __DIR__ . '/rest/routes/ProductCategoryRoutes.php';
require_once __DIR__ . '/rest/routes/OrderRoutes.php';
require_once __DIR__ . '/rest/routes/OrderProductRoutes.php';
require_once __DIR__ . '/rest/routes/WeatherRoutes.php';

Flight::start();
