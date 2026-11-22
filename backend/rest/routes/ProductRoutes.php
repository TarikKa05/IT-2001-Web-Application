<?php
Flight::route('GET /products', function () {
    Flight::json(Flight::product_service()->get_all());
});

Flight::route('GET /products/@id:[0-9]+', function ($id) {
    Flight::json(Flight::product_service()->get_by_id($id));
});

Flight::route('POST /products', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::product_service()->add($data), 201);
});

Flight::route('PUT /products/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::product_service()->update($data, $id));
});

Flight::route('DELETE /products/@id:[0-9]+', function ($id) {
    Flight::product_service()->delete($id);
    Flight::json(['message' => 'Product deleted.']);
});
