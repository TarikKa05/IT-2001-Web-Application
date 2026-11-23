<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../../utils/Roles.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';

class UserService extends BaseService
{
    protected $entity_name = 'User';
    private $order_dao;

    public function __construct($dao = null, $order_dao = null)
    {
        parent::__construct($dao ?? new UserDao());
        $this->order_dao = $order_dao ?? new OrderDao();
    }

    public function add($user)
    {
        $user['role'] = $user['role'] ?? Roles::CUSTOMER;
        $user = $this->normalize($user);
        $this->validate($user);
        Validator::required($user, ['password']);
        Validator::password($user['password']);

        $user['password_hash'] = password_hash($user['password'], PASSWORD_BCRYPT);

        $created = $this->dao->add($user);
        return $this->dao->get_by_id($created['id']);
    }

    public function update($user, $id)
    {
        $this->get_by_id($id);
        $user = $this->normalize($user);
        $this->validate($user, $id);

        if (!empty($user['password'])) {
            Validator::password($user['password']);
            $user['password_hash'] = password_hash($user['password'], PASSWORD_BCRYPT);
        }

        $this->dao->update($user, $id);
        return $this->dao->get_by_id($id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $orders = $this->order_dao->count_by_user($id);
        if ($orders > 0) {
            throw new ValidationException("This user still has $orders order(s) and cannot be deleted.");
        }
        $this->dao->delete($id);
    }

    protected function validate($user, $id = null)
    {
        Validator::required($user, ['name', 'username', 'email', 'phone_number', 'date_of_birth']);
        Validator::length($user['name'], 'name', 2, 50);
        Validator::no_html($user['name'], 'name');
        Validator::length($user['username'], 'username', 3, 50);
        Validator::no_html($user['username'], 'username');
        Validator::email($user['email']);
        Validator::length($user['email'], 'email', 0, 100);
        Validator::phone($user['phone_number']);
        Validator::date($user['date_of_birth'], 'date_of_birth');
        if ($user['date_of_birth'] >= date('Y-m-d')) {
            throw new ValidationException("Field 'date_of_birth' must be in the past.");
        }
        if (isset($user['role'])) {
            Validator::one_of($user['role'], 'role', [Roles::ADMIN, Roles::CUSTOMER]);
        }

        $existing = $this->dao->get_by_email($user['email']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("An account with this email already exists.");
        }

        $existing = $this->dao->get_by_username($user['username']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("The username '{$user['username']}' is already taken.");
        }
    }

    private function normalize($user)
    {
        if (isset($user['email'])) {
            $user['email'] = strtolower(trim($user['email']));
        }
        if (isset($user['username'])) {
            $user['username'] = trim($user['username']);
        }
        unset($user['password_hash']);
        return $user;
    }
}
