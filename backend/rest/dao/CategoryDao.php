<?php
require_once __DIR__ . '/BaseDao.php';

class CategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('categories', ['name', 'description']);
    }

    public function count_products_per_category()
    {
        return $this->query(
            "SELECT c.name AS category, COUNT(pc.id) AS total
             FROM categories c
             LEFT JOIN product_categories pc ON pc.category_id = c.id
             GROUP BY c.id, c.name
             ORDER BY c.id"
        );
    }
}
