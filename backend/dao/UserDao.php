<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {
    public function __construct() {
        parent::__construct("users");
    }

    public function createUser($user) {
        return $this->insert($user);
    }

    public function getUserById($id) {
        return $this->getById($id);
    }

    public function getAllUsers() {
        return $this->getAll();
    }

    public function updateUser($id, $user) {
        return $this->update($id, $user);
    }

    public function deleteUser($id) {
        return $this->delete($id);
    }

}
?>
