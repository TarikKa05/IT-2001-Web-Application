<?php
require_once __DIR__ . '/BaseDao.php';

class OrderDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('orders', ['user_id', 'total_amount', 'order_date']);
    }

    public function count_by_user($user_id)
    {
        return (int) $this->query_unique(
            "SELECT COUNT(*) AS total FROM orders WHERE user_id = :id",
            ['id' => $user_id]
        )['total'];
    }

    public function get_by_user($user_id)
    {
        return $this->query(
            "SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC",
            ['user_id' => $user_id]
        );
    }
}
