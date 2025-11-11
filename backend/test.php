<?php
require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/CategoryDao.php';
require_once __DIR__ . '/dao/ProductDao.php';
require_once __DIR__ . '/dao/ProductCategoryDao.php';
require_once __DIR__ . '/dao/OrderDao.php';
require_once __DIR__ . '/dao/OrderProductDao.php';

$userDao = new UserDao();
$categoryDao = new CategoryDao();
$productDao = new ProductDao();
$productCategoryDao = new ProductCategoryDao();
$orderDao = new OrderDao();
$orderProductDao = new OrderProductDao();

// --- Users (original example + one more) ---
$usersToInsert = [
    [
        'name' => 'John Doe',
        'username' => 'JohnDoe1',
        'email' => 'john@example.com',
        'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
        'phone_number' => '0612345678',
        'date_of_birth' => '2002-11-20',
        'role' => 'Customer'
    ],
    [
        'name' => 'Mia Carter',
        'username' => 'MiaCarter',
        'email' => 'mia@example.com',
        'password_hash' => password_hash('liftheavy', PASSWORD_DEFAULT),
        'phone_number' => '0611122233',
        'date_of_birth' => '1998-06-15',
        'role' => 'Trainer'
    ],
];
foreach ($usersToInsert as $user) {
    $userDao->insert($user);
}
$users = $userDao->getAll();
$usersByEmail = [];
foreach ($users as $userRow) {
    $usersByEmail[$userRow['email']] = $userRow['id'];
}

// --- Categories ---
$categoriesToInsert = [
    [
        'name' => 'Memberships',
        'description' => 'Recurring membership plans for the gym.',
    ],
    [
        'name' => 'Supplements',
        'description' => 'Protein powders, vitamins, and related products.',
    ],
];
foreach ($categoriesToInsert as $category) {
    $categoryDao->insert($category);
}
$categories = $categoryDao->getAllCategories();
$categoriesByName = [];
foreach ($categories as $categoryRow) {
    $categoriesByName[$categoryRow['name']] = $categoryRow['id'];
}

// --- Products ---
$productsToInsert = [
    [
        'name' => 'Monthly Gym Membership',
        'description' => 'Unlimited access to all facilities for 30 days.',
        'price' => 99.99,
        'stock_quantity' => 9999,
        'is_available' => 1,
    ],
    [
        'name' => 'Whey Protein 2kg',
        'description' => 'Chocolate flavored premium whey protein blend.',
        'price' => 59.50,
        'stock_quantity' => 120,
        'is_available' => 1,
    ],
];
foreach ($productsToInsert as $product) {
    $productDao->insert($product);
}
$products = $productDao->getAllProducts();
$productsByName = [];
foreach ($products as $productRow) {
    $productsByName[$productRow['name']] = $productRow['id'];
}

// --- Product <-> Category links ---
$productCategoryLinks = [
    [
        'product' => 'Monthly Gym Membership',
        'category' => 'Memberships',
    ],
    [
        'product' => 'Whey Protein 2kg',
        'category' => 'Supplements',
    ],
];
foreach ($productCategoryLinks as $link) {
    $productId = $productsByName[$link['product']] ?? null;
    $categoryId = $categoriesByName[$link['category']] ?? null;
    if ($productId && $categoryId) {
        $productCategoryDao->insert([
            'product_id' => $productId,
            'category_id' => $categoryId,
        ]);
    }
}

// --- Orders ---
$ordersToInsert = [
    [
        'user_email' => 'john@example.com',
        'total_amount' => 149.99,
        'order_date' => '2025-10-01 09:30:00',
    ],
    [
        'user_email' => 'mia@example.com',
        'total_amount' => 59.50,
        'order_date' => '2025-10-02 14:15:00',
    ],
];
foreach ($ordersToInsert as $order) {
    $userId = $usersByEmail[$order['user_email']] ?? null;
    if ($userId) {
        $orderDao->insert([
            'user_id' => $userId,
            'total_amount' => $order['total_amount'],
            'order_date' => $order['order_date'],
        ]);
    }
}
$orders = $orderDao->getAllOrders();

function findOrderId(array $orders, int $userId, string $orderDate): ?int {
    foreach ($orders as $order) {
        if ((int)$order['user_id'] === $userId && $order['order_date'] === $orderDate) {
            return (int)$order['id'];
        }
    }
    return null;
}

$orderIdsByDate = [];
foreach ($ordersToInsert as $order) {
    $userId = $usersByEmail[$order['user_email']] ?? null;
    if ($userId) {
        $orderIdsByDate[$order['order_date']] = findOrderId($orders, $userId, $order['order_date']);
    }
}

// --- Order Products ---
$orderProductsToInsert = [
    [
        'order_date' => '2025-10-01 09:30:00',
        'product_name' => 'Monthly Gym Membership',
        'quantity' => 1,
        'unit_price' => 99.99,
    ],
    [
        'order_date' => '2025-10-02 14:15:00',
        'product_name' => 'Whey Protein 2kg',
        'quantity' => 2,
        'unit_price' => 59.50,
    ],
];
foreach ($orderProductsToInsert as $orderProduct) {
    $orderId = $orderIdsByDate[$orderProduct['order_date']] ?? null;
    $productId = $productsByName[$orderProduct['product_name']] ?? null;
    if ($orderId && $productId) {
        $orderProductDao->insert([
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => $orderProduct['quantity'],
            'unit_price' => $orderProduct['unit_price'],
        ]);
    }
}

// Output everything for quick verification.
echo "Users:\n";
print_r($userDao->getAll());

echo "\nCategories:\n";
print_r($categoryDao->getAllCategories());

echo "\nProducts:\n";
print_r($productDao->getAllProducts());

echo "\nProduct Categories:\n";
print_r($productCategoryDao->getAllProductCategories());

echo "\nOrders:\n";
print_r($orderDao->getAllOrders());

echo "\nOrder Products:\n";
print_r($orderProductDao->getAllOrderProducts());
?>
