<?php
/**
* @OA\Get(
*     path="/productcategory/{id}",
*     tags={"product_categories"},
*     summary="Get product-category link by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Relation details"
*     )
* )
*/
// Get a specific productCategory by ID
Flight::route('GET /productcategory/@id', function($id){
   Flight::json(Flight::productCategoryService()->getProductCategoryById($id));
});

/**
* @OA\Get(
*     path="/productcategories",
*     tags={"product_categories"},
*     summary="Get all product-category relations",
*     @OA\Response(
*         response=200,
*         description="List of relations"
*     )
* )
*/
// Get all productCategorys
Flight::route('GET /productcategories/', function(){
   Flight::json(Flight::productCategoryService()->getAllProductCategories());
});

/**
* @OA\Post(
*     path="/productcategory",
*     tags={"product_categories"},
*     summary="Create product-category relation",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"product_id","category_id"},
*             @OA\Property(property="product_id", type="integer", example=1),
*             @OA\Property(property="category_id", type="integer", example=2)
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Created relation"
*     )
* )
*/
// Add a new productCategory
Flight::route('POST /productcategory/', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productCategoryService()->createProductCategory($data));
});

/**
* @OA\Put(
*     path="/productcategory/{id}",
*     tags={"product_categories"},
*     summary="Update product-category relation",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             @OA\Property(property="product_id", type="integer"),
*             @OA\Property(property="category_id", type="integer")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Updated relation"
*     )
* )
*/
// Update productCategory by ID
Flight::route('PUT /productcategory/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productCategoryService()->updateProductCategory($id, $data));
});

/**
* @OA\Delete(
*     path="/productcategory/{id}",
*     tags={"product_categories"},
*     summary="Delete product-category relation",
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
// Delete productCategory by ID
Flight::route('DELETE /productcategory/@id', function($id){
   Flight::json(Flight::productCategoryService()->deleteProductCategory($id));
});
?>
