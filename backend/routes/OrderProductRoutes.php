<?php
// Get a specific orderproduct by ID
Flight::route('GET /orderproduct/@id', function($id){
   Flight::json(Flight::orderProductService()->getOrderProductById($id));
});

// Get all orderproducts
Flight::route('GET /orderproducts/', function(){
   Flight::json(Flight::orderProductService()->getAllOrderProducts());
});


// Add a new orderproduct
Flight::route('POST /orderproduct', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderProductService()->createOrderProduct($data));
});

// Update orderproduct by ID
Flight::route('PUT /orderproduct/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderProductService()->updateOrderProduct($id, $data));
});


// Delete orderproduct by ID
Flight::route('DELETE /orderproduct/@id', function($id){
   Flight::json(Flight::orderProductService()->deleteOrderProduct($id));
});
?>
