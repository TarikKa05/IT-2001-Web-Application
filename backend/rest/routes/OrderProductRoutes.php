<?php
Flight::route('GET /order-products', function () {
    Flight::json(Flight::order_product_service()->get_all());
});

Flight::route('GET /order-products/@id:[0-9]+', function ($id) {
    Flight::json(Flight::order_product_service()->get_by_id($id));
});

Flight::route('POST /order-products', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_product_service()->add($data), 201);
});

Flight::route('PUT /order-products/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::order_product_service()->update($data, $id));
});

Flight::route('DELETE /order-products/@id:[0-9]+', function ($id) {
    Flight::order_product_service()->delete($id);
    Flight::json(['message' => 'Order product deleted.']);
});
