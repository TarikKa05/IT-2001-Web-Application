<?php
/**
* @OA\Get(
*     path="/product/{id}",
*     tags={"products"},
*     summary="Get product by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Product details"
*     )
* )
*/
// Get a specific product by ID
Flight::route('GET /product/@id', function($id){
   Flight::json(Flight::productService()->getProductById($id));
});

/**
* @OA\Get(
*     path="/products",
*     tags={"products"},
*     summary="Get all products",
*     @OA\Response(
*         response=200,
*         description="List of products"
*     )
* )
*/
// Get all products
Flight::route('GET /products/', function(){
   Flight::json(Flight::productService()->getAllProducts());
});

/**
* @OA\Post(
*     path="/product",
*     tags={"products"},
*     summary="Create product",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name","description","price","stock_quantity","is_available"},
*             @OA\Property(property="name", type="string", example="Protein Powder"),
*             @OA\Property(property="description", type="string", example="Chocolate whey protein"),
*             @OA\Property(property="price", type="number", format="float", example=39.99),
*             @OA\Property(property="stock_quantity", type="integer", example=50),
*             @OA\Property(property="is_available", type="boolean", example=true)
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Created product"
*     )
* )
*/
// Add a new product
Flight::route('POST /product/', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productService()->createProduct($data));
});

/**
* @OA\Put(
*     path="/product/{id}",
*     tags={"products"},
*     summary="Update product",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             @OA\Property(property="name", type="string"),
*             @OA\Property(property="description", type="string"),
*             @OA\Property(property="price", type="number", format="float"),
*             @OA\Property(property="stock_quantity", type="integer"),
*             @OA\Property(property="is_available", type="boolean")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Updated product"
*     )
* )
*/
// Update product by ID
Flight::route('PUT /product/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productService()->updateProduct($id, $data));
});

/**
* @OA\Delete(
*     path="/product/{id}",
*     tags={"products"},
*     summary="Delete product",
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
// Delete product by ID
Flight::route('DELETE /product/@id', function($id){
   Flight::json(Flight::productService()->deleteProduct($id));
});

/**
* @OA\Get(
*     path="/productsbycategoryname",
*     tags={"products"},
*     summary="Products by category name",
*     @OA\Parameter(
*         name="category_name",
*         in="query",
*         required=true,
*         @OA\Schema(type="string", example="Supplements")
*     ),
*     @OA\Response(
*         response=200,
*         description="Filtered products"
*     )
* )
*/
Flight::route('GET /productsbycategoryname/', function(){
    $data = Flight::request()->data->getData();
   Flight::json(Flight::productService()->getProductsByCategoryName($data));
});
?>
