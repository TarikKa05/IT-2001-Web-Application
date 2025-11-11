<?php
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/ProductService.php';
require_once __DIR__ . '/services/ProductCategoryService.php';
require_once __DIR__ . '/services/OrderService.php';
require_once __DIR__ . '/services/OrderProductService.php';

$userService = new UserService();
$categoryService = new CategoryService();
$productService = new ProductService();
$productCategoryService = new ProductCategoryService();
$orderService = new OrderService();
$orderProductService = new OrderProductService();

$tests = [
    'users.getAllUsers' => $userService->getAllUsers(),
    'categories.getAllCategories' => $categoryService->getAllCategories(),
    'products.getAllProducts' => $productService->getAllProducts(),
    'productCategories.getAllProductCategories' => $productCategoryService->getAllProductCategories(),
    'orders.getAllOrders' => $orderService->getAllOrders(),
    'orderProducts.getAllOrderProducts' => $orderProductService->getAllOrderProducts(),
];

foreach ($tests as $label => $result) {
    echo "=== $label ===\n";
    print_r($result);
    echo "\n";
}
?>
