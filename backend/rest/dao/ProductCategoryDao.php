<?php
require_once __DIR__ . '/BaseDao.php';

class ProductCategoryDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('product_categories', ['product_id', 'category_id']);
    }
}
