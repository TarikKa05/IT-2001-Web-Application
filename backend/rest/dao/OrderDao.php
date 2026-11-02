<?php
require_once __DIR__ . '/BaseDao.php';

class OrderDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('orders', ['user_id', 'total_amount', 'order_date']);
    }

    public function get_by_user($user_id)
    {
        return $this->query(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC",
            ['user_id' => $user_id]
        );
    }
}
