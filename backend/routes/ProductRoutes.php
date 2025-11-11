<?php
// Get a specific product by ID
Flight::route('GET /product/@id', function($id){
   Flight::json(Flight::productService()->getProductById($id));
});

// Get all products
Flight::route('GET /products/', function(){
   Flight::json(Flight::productService()->getAllProducts());
});


// Add a new product
Flight::route('POST /product/', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productService()->createProduct($data));
});

// Update product by ID
Flight::route('PUT /product/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productService()->updateProduct($id, $data));
});


// Delete product by ID
Flight::route('DELETE /product/@id', function($id){
   Flight::json(Flight::productService()->deleteProduct($id));
});


Flight::route('GET /productsbycategoryname/', function(){
    $data = Flight::request()->data->getData();
   Flight::json(Flight::productService()->getProductsByCategoryName($data));
});
?>