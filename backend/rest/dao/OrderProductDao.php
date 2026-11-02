<?php
require_once __DIR__ . '/BaseDao.php';

class OrderProductDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('order_products', ['order_id', 'product_id', 'quantity', 'unit_price']);
    }

    public function get_by_order($order_id)
    {
        return $this->query(
            "SELECT op.*, p.name AS product_name
             FROM order_products op
             JOIN products p ON p.id = op.product_id
             WHERE op.order_id = :order_id
             ORDER BY op.id",
            ['order_id' => $order_id]
        );
    }
}
