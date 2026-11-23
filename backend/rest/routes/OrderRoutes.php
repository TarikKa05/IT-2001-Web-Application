<?php
Flight::route('GET /orders', function () {
    Flight::json(Flight::order_service()->get_all());
});

Flight::route('GET /orders/@id:[0-9]+', function ($id) {
    Flight::json(Flight::order_service()->get_with_products($id));
});

Flight::route('POST /orders', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->add($data), 201);
});

Flight::route('POST /orders/place', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->place_order($data['user_id'] ?? null, $data['items'] ?? []), 201);
});

Flight::route('PUT /orders/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_service()->update($data, $id));
});

Flight::route('DELETE /orders/@id:[0-9]+', function ($id) {
    Flight::order_service()->delete($id);
    Flight::json(['message' => 'Order deleted.']);
});

Flight::route('GET /orders/@id:[0-9]+/products', function ($id) {
    Flight::json(Flight::order_product_service()->get_by_order($id));
});
