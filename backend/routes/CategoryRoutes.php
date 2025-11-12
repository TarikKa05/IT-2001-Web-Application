<?php
/**
* @OA\Get(
*     path="/category/{id}",
*     tags={"categories"},
*     summary="Get category by ID",
*     description="Retrieve a single category by its unique identifier.",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="Category ID",
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Returns the category with the given ID"
*     )
* )
*/

Flight::route('GET /category/@id', function($id){
   Flight::json(Flight::categoryService()->getCategoryById($id));
});

/**
* @OA\Get(
*      path="/categories",
*      tags={"categories"},
*      summary="Get all categories",
*      description="Fetch every category stored in the system.",
*      @OA\Response(
*           response=200,
*           description="Array of all categories in the database"
*      )
* )
*/

Flight::route('GET /categories/', function(){
   Flight::json(Flight::categoryService()->getAllCategories());
});

/**
* @OA\Post(
*     path="/category",
*     tags={"categories"},
*     summary="Add a new category",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name", "description"},
*             @OA\Property(property="name", type="string", example="Supplements"),
*             @OA\Property(property="description", type="string", example="Products that support workout recovery")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="New category created"
*     )
* )
*/

Flight::route('POST /category', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::categoryService()->createCategory($data));
});

/**
* @OA\Put(
*     path="/category/{id}",
*     tags={"categories"},
*     summary="Update an existing category by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="Category ID",
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name", "description"},
*             @OA\Property(property="name", type="string", example="Updated Category Name"),
*             @OA\Property(property="description", type="string", example="Updated description for the category")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Category updated"
*     )
* )
*/

Flight::route('PUT /category/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::categoryService()->updateCategory($id, $data));
});


/**
* @OA\Delete(
*     path="/category/{id}",
*     tags={"categories"},
*     summary="Delete a category by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         description="Category ID",
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="Category deleted"
*     )
* )
*/

Flight::route('DELETE /category/@id', function($id){
   Flight::json(Flight::categoryService()->deleteCategory($id));
});
?>
