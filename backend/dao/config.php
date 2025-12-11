<?php


// Set the reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED));


class Config
{
   public static function DB_NAME()
   {
       return 'gym_web_app'; 
   }
   public static function DB_PORT()
   {
       return  3306;
   }
   public static function DB_USER()
   {
       return 'root';
   }
   public static function DB_PASSWORD()
   {
       return '';
   }
   public static function DB_HOST()
   {
       return '127.0.0.1';
   }

   public static function JWT_SECRET() {
       return '166a340483a06142c502bbf3ed1a0d1ba4b77aa8682d83939fd102ed2c6aa37a';
   }
}
