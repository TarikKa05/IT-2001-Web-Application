<?php
require_once __DIR__ . '/BaseDao.php';

class ProductDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('products', ['name', 'description', 'price', 'stock_quantity', 'is_available']);
    }

    public function get_by_category($category_id)
    {
        return $this->query(
            "SELECT p.*
             FROM products p
             JOIN product_categories pc ON pc.product_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY p.id",
            ['category_id' => $category_id]
        );
    }

    public function change_stock($id, $delta)
    {
        return $this->execute(
            "UPDATE products SET stock_quantity = stock_quantity + :delta
             WHERE id = :id AND stock_quantity + :delta2 >= 0",
            ['delta' => $delta, 'delta2' => $delta, 'id' => $id]
        );
    }
}
