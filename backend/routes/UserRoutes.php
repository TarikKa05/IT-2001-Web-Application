<?php
// Get a specific user by ID
Flight::route('GET /user/@id', function($id){
   Flight::json(Flight::userService()->getUserById($id));
});

// Get all users
Flight::route('GET /users/', function(){
   Flight::json(Flight::userService()->getAllUsers());
});


// Add a new user
Flight::route('POST /user/', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::userService()->createUser($data));
});

// Update user by ID
Flight::route('PUT /user/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::userService()->updateUser($id, $data));
});


// Delete user by ID
Flight::route('DELETE /user/@id', function($id){
   Flight::json(Flight::userService()->deleteUser($id));
});


Flight::route('GET /userbyemail/@email', function($email){
   Flight::json(Flight::userService()->getUserByEmail($email));
});
?>