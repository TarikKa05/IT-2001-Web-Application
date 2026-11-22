<?php
require_once __DIR__ . '/UserService.php';
require_once __DIR__ . '/CategoryService.php';
require_once __DIR__ . '/ProductService.php';
require_once __DIR__ . '/ProductCategoryService.php';
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/OrderProductService.php';

function attempt($description, $action)
{
    echo str_pad($description, 50, '.') . ' ';
    try {
        $result = $action();
        echo "OK" . (isset($result['id']) ? " (id {$result['id']})" : '') . "\n";
        return $result;
    } catch (HttpException $e) {
        echo get_class($e) . " [{$e->get_status()}]: {$e->getMessage()}\n";
    }
}

$users = new UserService();
$categories = new CategoryService();
$products = new ProductService();
$product_categories = new ProductCategoryService();
$orders = new OrderService();
$order_products = new OrderProductService();

$valid_user = [
    'name' => 'Test User',
    'username' => 'testuser',
    'email' => 'Test.User@GymClub.ba',
    'password' => 'Secret123',
    'phone_number' => '062333444',
    'date_of_birth' => '2003-03-03'
];

echo "=== UserService ===\n";
attempt('add: no password', fn() => $users->add(array_diff_key($valid_user, ['password' => 0])));
attempt('add: password "123"', fn() => $users->add(['password' => '123'] + $valid_user));
attempt('add: email "Tarik@GymClub.ba" (exists)', fn() => $users->add(['email' => 'Tarik@GymClub.ba'] + $valid_user));
attempt('add: username "tarikk" (exists)', fn() => $users->add(['username' => 'tarikk'] + $valid_user));
attempt('add: email "not-an-email"', fn() => $users->add(['email' => 'not-an-email'] + $valid_user));
attempt('add: phone "06x123"', fn() => $users->add(['phone_number' => '06x123'] + $valid_user));
attempt('add: born tomorrow', fn() => $users->add(['date_of_birth' => date('Y-m-d', strtotime('+1 day'))] + $valid_user));
attempt('add: role "superuser"', fn() => $users->add(['role' => 'superuser'] + $valid_user));
$user = attempt('add: valid customer', fn() => $users->add($valid_user));
echo "      stored email: {$user['email']}, role: {$user['role']}\n";
echo "      hash returned by add(): " . (isset($user['password_hash']) ? 'yes' : 'no') . "\n";
echo "      password is hashed: " . (password_verify('Secret123', (new UserDao())->get_by_email($user['email'])['password_hash']) ? 'yes' : 'no') . "\n";
attempt('update: keeps its own email and username', fn() => $users->update(['phone_number' => '062999888'] + $valid_user, $user['id']));
attempt('get user #999', fn() => $users->get_by_id(999));
attempt('delete user #2 (has orders)', fn() => $users->delete(2));

echo "=== CategoryService ===\n";
attempt('add: no name', fn() => $categories->add(['description' => 'x']));
attempt('add: name "P"', fn() => $categories->add(['name' => 'P']));
attempt('add: name "Protein" (exists)', fn() => $categories->add(['name' => 'Protein']));
attempt('add: description with <script>', fn() => $categories->add(['name' => 'Vitamins', 'description' => '<script>alert(1)</script>']));
attempt('update #1: keeps its own name "Protein"', fn() => $categories->update(['name' => 'Protein', 'description' => 'Protein supplements for muscle building.'], 1));
$category = attempt('add: "Test Category"', fn() => $categories->add(['name' => 'Test Category', 'description' => '']));
attempt('delete #1 (Protein has products)', fn() => $categories->delete(1));
attempt('delete #999', fn() => $categories->delete(999));

echo "=== ProductService ===\n";
attempt('add: price -5', fn() => $products->add(['name' => 'Test Product', 'price' => -5, 'stock_quantity' => 5]));
attempt('add: stock "abc"', fn() => $products->add(['name' => 'Test Product', 'price' => 20, 'stock_quantity' => 'abc']));
attempt('add: name "CREATINE" (exists)', fn() => $products->add(['name' => 'CREATINE', 'price' => 20, 'stock_quantity' => 5]));
$product = attempt('add: valid product, is_available true', fn() => $products->add(['name' => 'Test Product', 'description' => 'Created by test.php', 'price' => 20, 'stock_quantity' => 5, 'is_available' => true]));
echo "      is_available stored as: {$product['is_available']}\n";
attempt('products of category #999', fn() => $products->get_by_category(999));
attempt('delete #1 WHEY PROTEIN (in orders)', fn() => $products->delete(1));

echo "=== ProductCategoryService ===\n";
attempt('add: product #999', fn() => $product_categories->add(['product_id' => 999, 'category_id' => 1]));
attempt('add: category "abc"', fn() => $product_categories->add(['product_id' => 1, 'category_id' => 'abc']));
attempt('add: WHEY PROTEIN to Protein again', fn() => $product_categories->add(['product_id' => 1, 'category_id' => 1]));
$link = attempt('add: Test Product to Test Category', fn() => $product_categories->add(['product_id' => $product['id'], 'category_id' => $category['id']]));
attempt('delete Test Category (has a product)', fn() => $categories->delete($category['id']));

echo "=== OrderService ===\n";
attempt('add: user #999', fn() => $orders->add(['user_id' => 999]));
attempt('place: no products', fn() => $orders->place_order($user['id'], []));
attempt('place: quantity 0', fn() => $orders->place_order($user['id'], [['product_id' => 1, 'quantity' => 0]]));
attempt('place: 50 x OMEGA 3 (stock 10)', fn() => $orders->place_order($user['id'], [['product_id' => 3, 'quantity' => 50]]));
attempt('place: same product twice', fn() => $orders->place_order($user['id'], [['product_id' => 1, 'quantity' => 1], ['product_id' => 1, 'quantity' => 1]]));
$order = attempt('place: 2 x WHEY (client price 1) + 1 x OMEGA', fn() => $orders->place_order($user['id'], [['product_id' => 1, 'quantity' => 2, 'unit_price' => 1], ['product_id' => 3, 'quantity' => 1]]));
echo "      total_amount: {$order['total_amount']}, products: " . count($order['products']) . "\n";
echo "      stock now: WHEY " . (new ProductDao())->get_by_id(1)['stock_quantity'] . ", OMEGA " . (new ProductDao())->get_by_id(3)['stock_quantity'] . "\n";
attempt('update: client sends total_amount 1', fn() => $orders->update(['user_id' => $user['id'], 'total_amount' => 1], $order['id']));
echo "      total_amount after update: " . $orders->get_by_id($order['id'])['total_amount'] . "\n";
attempt('orders of user #999', fn() => $orders->get_by_user(999));
attempt('get order #999', fn() => $orders->get_by_id(999));

echo "=== OrderProductService ===\n";
attempt('add: WHEY again to the same order', fn() => $order_products->add(['order_id' => $order['id'], 'product_id' => 1, 'quantity' => 1]));
attempt('add: order #999', fn() => $order_products->add(['order_id' => 999, 'product_id' => 2, 'quantity' => 1]));
$line = attempt('add: 1 x CREATINE, client price 0.01', fn() => $order_products->add(['order_id' => $order['id'], 'product_id' => 2, 'quantity' => 1, 'unit_price' => 0.01]));
echo "      unit_price: {$line['unit_price']}, order total: " . $orders->get_by_id($order['id'])['total_amount'] . "\n";
attempt('update CREATINE: quantity 100', fn() => $order_products->update(['quantity' => 100], $line['id']));
attempt('update CREATINE: quantity 3', fn() => $order_products->update(['quantity' => 3], $line['id']));
echo "      order total: " . $orders->get_by_id($order['id'])['total_amount'] . ", CREATINE stock: " . (new ProductDao())->get_by_id(2)['stock_quantity'] . "\n";
attempt('delete the CREATINE line', fn() => $order_products->delete($line['id']));
echo "      order total: " . $orders->get_by_id($order['id'])['total_amount'] . ", CREATINE stock: " . (new ProductDao())->get_by_id(2)['stock_quantity'] . "\n";
attempt('products of order #999', fn() => $order_products->get_by_order(999));

echo "=== Clean-up ===\n";
attempt('delete the order (stock goes back)', fn() => $orders->delete($order['id']));
echo "      stock now: WHEY " . (new ProductDao())->get_by_id(1)['stock_quantity'] . ", OMEGA " . (new ProductDao())->get_by_id(3)['stock_quantity'] . "\n";
attempt('delete the product link', fn() => $product_categories->delete($link['id']));
attempt('delete Test Product', fn() => $products->delete($product['id']));
attempt('delete Test Category', fn() => $categories->delete($category['id']));
attempt('delete the test user (no orders now)', fn() => $users->delete($user['id']));
