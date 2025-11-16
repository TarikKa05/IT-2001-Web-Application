<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
class UserService extends BaseService {
   public function __construct() {

       $dao = new UserDao();
       parent::__construct($dao);
   }

   public function createUser($user) {
        if (empty($user['name']) || empty($user['email']) || empty($user['password']) || empty($user['username']) || empty($user['phone_number']) || empty($user['date_of_birth'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format.');
        }

        if ($this->dao->getUserByEmail($user['email'])) {
            throw new InvalidArgumentException('Email is already in use.');
        }

        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);

        return $this->create($user);
   }

   public function getUserById($id) {
        return $this->getById($id);
   }
   public function getAllUsers() {
       return $this->getAll();
   }
   public function updateUser($id, $user) {
        if (empty($user['name']) || empty($user['email']) || empty($user['password'] || empty($user['username']) || empty($user['phone_number']) || empty($user['date_of_birth']))) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format.');
        }

        $existing = $this->dao->getUserByEmail($user['email']);
        if ($existing && $existing['id'] != $id) {
            throw new InvalidArgumentException('Email is already in use.');
        }

        if (!empty($user['password'])) {
            $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        } else {
            unset($user['password']);
        }
       return $this->update($id, $user);
   }
   public function deleteUser($id) {
       return $this->delete($id);
   }
   public function getUserByEmail($email) {
       return $this->dao->getUserByEmail($email);
   }
}

?>
