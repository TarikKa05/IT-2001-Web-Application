<?php
require_once __DIR__ . '/BaseDao.php';

class ProductCategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('product_categories', ['product_id', 'category_id']);
    }

    public function get_by_product_and_category($product_id, $category_id)
    {
        return $this->query_unique(
            "SELECT * FROM product_categories WHERE product_id = :product_id AND category_id = :category_id",
            ['product_id' => $product_id, 'category_id' => $category_id]
        );
    }

    public function count_by_category($category_id)
    {
        return (int) $this->query_unique(
            "SELECT COUNT(*) AS total FROM product_categories WHERE category_id = :id",
            ['id' => $category_id]
        )['total'];
    }

    public function delete_by_product($product_id)
    {
        return $this->execute("DELETE FROM product_categories WHERE product_id = :product_id", ['product_id' => $product_id]);
    }
}
