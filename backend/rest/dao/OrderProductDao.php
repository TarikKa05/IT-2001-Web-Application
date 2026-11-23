<?php
require_once __DIR__ . '/BaseDao.php';

class OrderProductDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('order_products', ['order_id', 'product_id', 'quantity', 'unit_price']);
    }

    public function get_by_order_and_product($order_id, $product_id)
    {
        return $this->query_unique(
            "SELECT * FROM order_products WHERE order_id = :order_id AND product_id = :product_id",
            ['order_id' => $order_id, 'product_id' => $product_id]
        );
    }

    public function count_by_product($product_id)
    {
        return (int) $this->query_unique(
            "SELECT COUNT(*) AS total FROM order_products WHERE product_id = :id",
            ['id' => $product_id]
        )['total'];
    }

    public function delete_by_order($order_id)
    {
        return $this->execute("DELETE FROM order_products WHERE order_id = :order_id", ['order_id' => $order_id]);
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
