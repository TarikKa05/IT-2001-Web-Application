<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
Flight::group('/auth', function() {
   /**
 * @OA\Post(
 *     path="/auth/register",
 *     tags={"auth"},
 *     summary="Register new user",
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
 *         description="User has been added."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */

   Flight::route("POST /register", function () {
       $data = Flight::request()->data->getData();


       $response = Flight::auth_service()->register($data);
  
       if ($response['success']) {
           Flight::json([
               'message' => 'User registered successfully',
               'data' => $response['data']
           ]);
       } else {
           Flight::halt(500, $response['error']);
       }
   });
   /**
    * @OA\Post(
    *      path="/auth/login",
    *      tags={"auth"},
    *      summary="Login to system using email and password",
    *      @OA\Response(
    *           response=200,
    *           description="Student data and JWT"
    *      ),
    *      @OA\RequestBody(
    *          description="Credentials",
    *          @OA\JsonContent(
    *              required={"email","password"},
    *              @OA\Property(property="email", type="string", example="demo@gmail.com", description="Student email address"),
    *              @OA\Property(property="password", type="string", example="some_password", description="Student password")
    *          )
    *      )
    * )
    */
   Flight::route('POST /login', function() {
       $data = Flight::request()->data->getData();


       $response = Flight::auth_service()->login($data);
  
       if ($response['success']) {
           Flight::json([
               'message' => 'User logged in successfully',
               'data' => $response['data']
           ]);
       } else {
           Flight::halt(500, $response['error']);
       }
   });
});
?>
