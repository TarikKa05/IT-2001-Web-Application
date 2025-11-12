<?php
/**
* @OA\Get(
*     path="/order/{id}",
*     tags={"orders"},
*     summary="Get order by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Order details"
*     )
* )
*/
// Get a specific order by ID
Flight::route('GET /order/@id', function($id){
   Flight::json(Flight::orderService()->getOrderById($id));
});

/**
* @OA\Get(
*     path="/orders",
*     tags={"orders"},
*     summary="Get all orders",
*     @OA\Response(
*         response=200,
*         description="List of orders"
*     )
* )
*/
// Get all orders
Flight::route('GET /orders/', function(){
   Flight::json(Flight::orderService()->getAllOrders());
});

/**
* @OA\Post(
*     path="/order",
*     tags={"orders"},
*     summary="Create order",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"user_id","total_amount"},
*             @OA\Property(property="user_id", type="integer", example=1),
*             @OA\Property(property="total_amount", type="number", format="float", example=120.50)
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Created order"
*     )
* )
*/
// Add a new order
Flight::route('POST /order', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderService()->createOrder($data));
});

/**
* @OA\Put(
*     path="/order/{id}",
*     tags={"orders"},
*     summary="Update order",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             @OA\Property(property="user_id", type="integer"),
*             @OA\Property(property="total_amount", type="number", format="float"),
*             @OA\Property(property="order_date", type="string", format="date-time")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Updated order"
*     )
* )
*/
// Update order by ID
Flight::route('PUT /order/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderService()->updateOrder($id, $data));
});

/**
* @OA\Delete(
*     path="/order/{id}",
*     tags={"orders"},
*     summary="Delete order",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Deletion status"
*     )
* )
*/
// Delete order by ID
Flight::route('DELETE /order/@id', function($id){
   Flight::json(Flight::orderService()->deleteOrder($id));
});
?>
