<?php
require_once 'dao/UserDao.php';


$userDao = new UserDao();

//Script to add a user to the database 
//Insert a new user 
$userDao->insert([
   'name' => 'John Doe',
   'username' => 'JohnDoe1',
   'email' => 'john@example.com',
   'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
   'phone_number' => '0612345678',
   'date_of_birth' => '02.11.2005.',
   'role' => 'Customer'
]);


//Fetch all users
$users = $userDao->getAll();
print_r($users);





?>