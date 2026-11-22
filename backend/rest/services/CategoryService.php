<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';

class CategoryService extends BaseService
{
    protected $entity_name = 'Category';
    private $product_category_dao;

    public function __construct($dao = null, $product_category_dao = null)
    {
        parent::__construct($dao ?? new CategoryDao());
        $this->product_category_dao = $product_category_dao ?? new ProductCategoryDao();
    }

    public function add($category)
    {
        return parent::add($this->empty_to_null($category, ['description']));
    }

    public function update($category, $id)
    {
        return parent::update($this->empty_to_null($category, ['description']), $id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $products = $this->product_category_dao->count_by_category($id);
        if ($products > 0) {
            throw new ValidationException("This category still contains $products product(s). Move or remove them first.");
        }
        $this->dao->delete($id);
    }

    public function count_products_per_category()
    {
        return $this->dao->count_products_per_category();
    }

    protected function validate($category, $id = null)
    {
        Validator::required($category, ['name']);
        Validator::length($category['name'], 'name', 2, 50);
        Validator::no_html($category['name'], 'name');
        if (!empty($category['description'])) {
            Validator::length($category['description'], 'description', 0, 500);
            Validator::no_html($category['description'], 'description');
        }

        $existing = $this->dao->get_by_name(trim($category['name']));
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A category named '{$category['name']}' already exists.");
        }
    }
}
