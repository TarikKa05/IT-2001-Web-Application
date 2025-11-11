<?php
// Get a specific category by ID
Flight::route('GET /category/@id', function($id){
   Flight::json(Flight::categoryService()->getCategoryById($id));
});

// Get all categories
Flight::route('GET /categories/', function(){
   Flight::json(Flight::categoryService()->getAllCategories());
});


// Add a new category
Flight::route('POST /category', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::categoryService()->createCategory($data));
});

// Update category by ID
Flight::route('PUT /category/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::categoryService()->updateCategory($id, $data));
});


// Delete category by ID
Flight::route('DELETE /category/@id', function($id){
   Flight::json(Flight::categoryService()->deleteCategory($id));
});
?>
