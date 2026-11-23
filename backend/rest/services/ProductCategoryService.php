<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class ProductCategoryService extends BaseService
{
    protected $entity_name = 'Product category';
    private $product_dao;
    private $category_dao;

    public function __construct($dao = null, $product_dao = null, $category_dao = null)
    {
        parent::__construct($dao ?? new ProductCategoryDao());
        $this->product_dao = $product_dao ?? new ProductDao();
        $this->category_dao = $category_dao ?? new CategoryDao();
    }

    protected function validate($product_category, $id = null)
    {
        Validator::required($product_category, ['product_id', 'category_id']);
        Validator::integer($product_category['product_id'], 'product_id', 1);
        Validator::integer($product_category['category_id'], 'category_id', 1);

        $product = $this->product_dao->get_by_id($product_category['product_id']);
        if (!$product) {
            throw new ValidationException("The selected product does not exist.");
        }
        $category = $this->category_dao->get_by_id($product_category['category_id']);
        if (!$category) {
            throw new ValidationException("The selected category does not exist.");
        }

        $existing = $this->dao->get_by_product_and_category($product_category['product_id'], $product_category['category_id']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("'{$product['name']}' is already in the category '{$category['name']}'.");
        }
    }
}
