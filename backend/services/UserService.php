<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
class UserService extends BaseService {
   public function __construct() {

       $dao = new UserDao();
       parent::__construct($dao);
   }

   public function createUser($user) {
       return $this->dao->createUser($user);
   }
   public function getUserById($id) {
       return $this->dao->getUserById($id);
   }
   public function getAllUsers() {
       return $this->dao->getAllUsers();
   }
   public function updateUser($id, $user) {
       return $this->dao->updateUser($id, $user);
   }
   public function deleteUser($id) {
       return $this->dao->deleteUser($id);
   }
   public function getUserByEmail($email) {
       return $this->dao->getUserByEmail($email);
   }
}

?>
