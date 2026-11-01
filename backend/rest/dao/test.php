<?php
require_once __DIR__ . '/UserDao.php';
require_once __DIR__ . '/CategoryDao.php';
require_once __DIR__ . '/ProductDao.php';
require_once __DIR__ . '/ProductCategoryDao.php';
require_once __DIR__ . '/OrderDao.php';
require_once __DIR__ . '/OrderProductDao.php';

$user_dao = new UserDao();
$category_dao = new CategoryDao();
$product_dao = new ProductDao();
$product_category_dao = new ProductCategoryDao();
$order_dao = new OrderDao();
$order_product_dao = new OrderProductDao();

echo "===== READ =====\n";
echo "Users: " . $user_dao->count_all() . "\n";
echo "Categories: " . $category_dao->count_all() . "\n";
echo "Products: " . $product_dao->count_all() . "\n";
echo "Product categories: " . $product_category_dao->count_all() . "\n";
echo "Orders: " . $order_dao->count_all() . "\n";
echo "Order products: " . $order_product_dao->count_all() . "\n";
print_r($user_dao->get_all());
print_r($user_dao->get_by_id(2));
print_r($user_dao->get_by_email('tarik@gymclub.ba'));
print_r($category_dao->get_all());
print_r($category_dao->count_products_per_category());
print_r($product_dao->get_all());
print_r($product_dao->get_by_category(1));
print_r($product_category_dao->get_all());
print_r($order_dao->get_by_user(2));
print_r($order_product_dao->get_by_order(1));

echo "===== CREATE =====\n";
$user = $user_dao->add([
    'name' => 'Test User',
    'username' => 'testuser',
    'email' => 'test@gymclub.ba',
    'password_hash' => password_hash('test123', PASSWORD_BCRYPT),
    'phone_number' => '062333444',
    'date_of_birth' => '2003-03-03',
    'role' => 'customer',
    'hacker' => 'DROP TABLE'
]);
print_r($user);

$category = $category_dao->add(['name' => 'Test category', 'description' => 'Created by test.php', 'hacker' => 'DROP TABLE']);
print_r($category);

$product = $product_dao->add([
    'name' => 'TEST PRODUCT',
    'description' => 'Created by test.php',
    'price' => 25.00,
    'stock_quantity' => 5,
    'is_available' => 1,
    'hacker' => 'DROP TABLE'
]);
print_r($product);

$product_category = $product_category_dao->add(['product_id' => $product['id'], 'category_id' => $category['id']]);
print_r($product_category);

$order = $order_dao->add(['user_id' => $user['id'], 'total_amount' => 50.00, 'order_date' => '2026-11-01 12:00:00']);
print_r($order);

$order_product = $order_product_dao->add(['order_id' => $order['id'], 'product_id' => $product['id'], 'quantity' => 2, 'unit_price' => 25.00]);
print_r($order_product);

echo "===== UPDATE =====\n";
echo "Rows changed: " . $user_dao->update(['phone_number' => '062999888'], $user['id']) . "\n";
print_r($user_dao->get_by_id($user['id']));
echo "Rows changed: " . $category_dao->update(['description' => 'Updated description'], $category['id']) . "\n";
print_r($category_dao->get_by_id($category['id']));
echo "Rows changed: " . $product_dao->update(['price' => 27.50], $product['id']) . "\n";
print_r($product_dao->get_by_id($product['id']));
echo "Rows changed: " . $product_category_dao->update(['category_id' => 1], $product_category['id']) . "\n";
print_r($product_category_dao->get_by_id($product_category['id']));
echo "Rows changed: " . $order_dao->update(['total_amount' => 55.00], $order['id']) . "\n";
print_r($order_dao->get_by_id($order['id']));
echo "Rows changed: " . $order_product_dao->update(['quantity' => 3], $order_product['id']) . "\n";
print_r($order_product_dao->get_by_id($order_product['id']));

echo "===== STOCK =====\n";
echo "Take 3 from stock 5: " . $product_dao->change_stock($product['id'], -3) . "\n";
echo "Take 10 from stock 2: " . $product_dao->change_stock($product['id'], -10) . "\n";
echo "Stock now: " . $product_dao->get_by_id($product['id'])['stock_quantity'] . "\n";

echo "===== DELETE =====\n";
$order_product_dao->delete($order_product['id']);
var_dump($order_product_dao->get_by_id($order_product['id']));
$order_dao->delete($order['id']);
var_dump($order_dao->get_by_id($order['id']));
$product_category_dao->delete($product_category['id']);
var_dump($product_category_dao->get_by_id($product_category['id']));
$product_dao->delete($product['id']);
var_dump($product_dao->get_by_id($product['id']));
$category_dao->delete($category['id']);
var_dump($category_dao->get_by_id($category['id']));
$user_dao->delete($user['id']);
var_dump($user_dao->get_by_id($user['id']));
