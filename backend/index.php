<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require 'vendor/autoload.php'; //run autoloader

require_once __DIR__ . '/services/CategoryService.php';
Flight::register('categoryService', 'CategoryService'); 

require_once __DIR__ . '/services/OrderService.php';
Flight::register('orderService', 'OrderService');

require_once __DIR__ . '/services/OrderProductService.php';
Flight::register('orderProductService', 'OrderProductService');

require_once __DIR__ . '/services/ProductCategoryService.php';
Flight::register('productCategoryService', 'ProductCategoryService');

require_once __DIR__ . '/services/ProductService.php';
Flight::register('productService', 'ProductService');

require_once __DIR__ . '/services/UserService.php';
Flight::register('userService', 'UserService');

require 'rest/services/AuthService.php';
Flight::register('auth_service', "AuthService");
require_once __DIR__ .'rest/routes/AuthRoutes.php';

// This wildcard route intercepts all requests and applies authentication checks before proceeding.
Flight::route('/*', function() {
   if(
       strpos(Flight::request()->url, '/auth/login') === 0 ||
       strpos(Flight::request()->url, '/auth/register') === 0
   ) {
       return TRUE;
   } else {
       try {
           $token = Flight::request()->getHeader("Authentication");
           if(!$token)
               Flight::halt(401, "Missing authentication header");


           $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));


           Flight::set('user', $decoded_token->user);
           Flight::set('jwt_token', $token);
           return TRUE;
       } catch (\Exception $e) {
           Flight::halt(401, $e->getMessage());
       }
   }
});


require_once __DIR__ . '/routes/OrderRoutes.php';
require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/OrderProductRoutes.php';
require_once __DIR__ . '/routes/ProductCategoryRoutes.php';
require_once __DIR__ . '/routes/ProductRoutes.php';
require_once __DIR__ . '/routes/UserRoutes.php';
Flight::start();



?>
