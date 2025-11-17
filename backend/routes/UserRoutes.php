<?php
/**
* @OA\Get(
*     path="/user/{id}",
*     tags={"users"},
*     summary="Get user by ID",
*     @OA\Parameter(
*         name="id",
*         in="path",
*         required=true,
*         @OA\Schema(type="integer", example=1)
*     ),
*     @OA\Response(
*         response=200,
*         description="User details"
*     )
* )
*/
// Get a specific user by ID
Flight::route('GET /user/@id', function($id){
   Flight::json(Flight::userService()->getUserById($id));
});

/**
* @OA\Get(
*     path="/users",
*     tags={"users"},
*     summary="Get all users",
*     @OA\Response(
*         response=200,
*         description="List of users"
*     )
* )
*/
// Get all users
Flight::route('GET /users/', function(){
   Flight::json(Flight::userService()->getAllUsers());
});

/**
* @OA\Post(
*     path="/user",
*     tags={"users"},
*     summary="Create user",
*     @OA\RequestBody(
*         required=true,
*         @OA\JsonContent(
*             required={"name","username","email","password_hash","phone_number","date_of_birth","role"},
*             @OA\Property(property="name", type="string", example="John Doe"),
*             @OA\Property(property="username", type="string", example="johnd"),
*             @OA\Property(property="email", type="string", example="john@example.com"),
*             @OA\Property(property="password_hash", type="string", example="$2y$10$saltedhash"),
*             @OA\Property(property="phone_number", type="string", example="061234567"),
*             @OA\Property(property="date_of_birth", type="string", format="date", example="2000-01-01"),
*             @OA\Property(property="role", type="string", example="Customer")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Created user"
*     )
* )
*/
// Add a new user
Flight::route('POST /user/', function(){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::userService()->createUser($data));
});

/**
* @OA\Put(
*     path="/user/{id}",
*     tags={"users"},
*     summary="Update user",
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
*             @OA\Property(property="username", type="string"),
*             @OA\Property(property="email", type="string"),
*             @OA\Property(property="password_hash", type="string"),
*             @OA\Property(property="phone_number", type="string"),
*             @OA\Property(property="date_of_birth", type="string", format="date"),
*             @OA\Property(property="role", type="string")
*         )
*     ),
*     @OA\Response(
*         response=200,
*         description="Updated user"
*     )
* )
*/
// Update user by ID
Flight::route('PUT /user/@id', function($id){
   $data = Flight::request()->data->getData();
   Flight::json(Flight::userService()->updateUser($id, $data));
});

/**
* @OA\Delete(
*     path="/user/{id}",
*     tags={"users"},
*     summary="Delete user",
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
// Delete user by ID
Flight::route('DELETE /user/@id', function($id){
   Flight::json(Flight::userService()->deleteUser($id));
});

/**
* @OA\Get(
*     path="/userbyemail/{email}",
*     tags={"users"},
*     summary="Get user by email",
*     @OA\Parameter(
*         name="email",
*         in="path",
*         required=true,
*         @OA\Schema(type="string", example="john@example.com")
*     ),
*     @OA\Response(
*         response=200,
*         description="User details"
*     )
* )
*/
Flight::route('GET /userbyemail/@email', function($email){
   Flight::json(Flight::userService()->getUserByEmail($email));
});
?>
