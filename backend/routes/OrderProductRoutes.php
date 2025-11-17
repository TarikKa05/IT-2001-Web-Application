<?php
/**
* @OA\Get(
*     path="/orderproduct/{id}",
*     tags={"order_products"},
*     summary="Get order-product record by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Order-product details"
*     )
* )
*/
// Get a specific orderproduct by ID
Flight::route('GET /orderproduct/@id', function($id){
   Flight::json(Flight::orderProductService()->getOrderProductById($id));
});

/**
* @OA\Get(
*     path="/orderproducts",
*     tags={"order_products"},
*     summary="Get all order-product records",
*     @OA\Response(
*         response=200,
*         description="List of relations"
*     )
* )
*/
// Get all orderproducts
Flight::route('GET /orderproducts/', function(){
   Flight::json(Flight::orderProductService()->getAllOrderProducts());
});

/**
* @OA\Post(
*     path="/orderproduct",
*     tags={"order_products"},
*     summary="Create order-product record",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"order_id","product_id","quantity","unit_price"},
*             @OA\Property(property="order_id", type="integer", example=1),
*             @OA\Property(property="product_id", type="integer", example=3),
*             @OA\Property(property="quantity", type="integer", example=2),
*             @OA\Property(property="unit_price", type="number", format="float", example=19.99)
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Created record"
*     )
* )
*/
// Add a new orderproduct
Flight::route('POST /orderproduct', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderProductService()->createOrderProduct($data));
});

/**
* @OA\Put(
*     path="/orderproduct/{id}",
*     tags={"order_products"},
*     summary="Update order-product record",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             @OA\Property(property="order_id", type="integer"),
*             @OA\Property(property="product_id", type="integer"),
*             @OA\Property(property="quantity", type="integer"),
*             @OA\Property(property="unit_price", type="number", format="float")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Updated record"
*     )
* )
*/
// Update orderproduct by ID
Flight::route('PUT /orderproduct/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::orderProductService()->updateOrderProduct($id, $data));
});

/**
* @OA\Delete(
*     path="/orderproduct/{id}",
*     tags={"order_products"},
*     summary="Delete order-product record",
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
// Delete orderproduct by ID
Flight::route('DELETE /orderproduct/@id', function($id){
   Flight::json(Flight::orderProductService()->deleteOrderProduct($id));
});
?>
