<?php
Flight::route('GET /categories', function () {
    Flight::json(Flight::category_service()->get_all());
});

Flight::route('GET /categories/stats', function () {
    Flight::json(Flight::category_service()->count_products_per_category());
});

Flight::route('GET /categories/@id:[0-9]+', function ($id) {
    Flight::json(Flight::category_service()->get_by_id($id));
});

Flight::route('POST /categories', function () {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::category_service()->add($data), 201);
});

Flight::route('PUT /categories/@id:[0-9]+', function ($id) {
    $data = Flight::request()->data->getData();
    Flight::json(Flight::category_service()->update($data, $id));
});

Flight::route('DELETE /categories/@id:[0-9]+', function ($id) {
    Flight::category_service()->delete($id);
    Flight::json(['message' => 'Category deleted.']);
});

Flight::route('GET /categories/@id:[0-9]+/products', function ($id) {
    Flight::json(Flight::product_service()->get_by_category($id));
});
