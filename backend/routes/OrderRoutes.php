<?php
// Get a specific order by ID
Flight::route('GET /order/@id', function($id){
   Flight::json(Flight::orderService()->getOrderById($id));
});

// Get all orders
Flight::route('GET /orders/', function(){
   Flight::json(Flight::orderService()->getAllOrders());
});


// Add a new order
Flight::route('POST /order', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderService()->createOrder($data));
});


// Update order by ID
Flight::route('PUT /order/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderService()->updateOrder($id, $data));
});


// Delete order by ID
Flight::route('DELETE /order/@id', function($id){
   Flight::json(Flight::orderService()->deleteOrder($id));
});
?>
