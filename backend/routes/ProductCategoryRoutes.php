<?php
// Get a specific productCategory by ID
Flight::route('GET /productcategory/@id', function($id){
   Flight::json(Flight::productCategoryService()->getProductCategoryById($id));
});

// Get all productCategorys
Flight::route('GET /productcategories/', function(){
   Flight::json(Flight::productCategoryService()->getAllProductCategories());
});


// Add a new productCategory
Flight::route('POST /productcategory/', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productCategoryService()->createProductCategory($data));
});

// Update productCategory by ID
Flight::route('PUT /productcategory/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::productCategoryService()->updateProductCategory($id, $data));
});


// Delete productCategory by ID
Flight::route('DELETE /productcategory/@id', function($id){
   Flight::json(Flight::productCategoryService()->deleteProductCategory($id));
});
?>
