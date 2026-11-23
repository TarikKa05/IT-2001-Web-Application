<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';
require_once __DIR__ . '/../dao/OrderProductDao.php';

class ProductService extends BaseService
{
    protected $entity_name = 'Product';
    private $category_dao;
    private $product_category_dao;
    private $order_product_dao;

    public function __construct($dao = null, $category_dao = null, $product_category_dao = null, $order_product_dao = null)
    {
        parent::__construct($dao ?? new ProductDao());
        $this->category_dao = $category_dao ?? new CategoryDao();
        $this->product_category_dao = $product_category_dao ?? new ProductCategoryDao();
        $this->order_product_dao = $order_product_dao ?? new OrderProductDao();
    }

    public function add($product)
    {
        return parent::add($this->normalize($product));
    }

    public function update($product, $id)
    {
        return parent::update($this->normalize($product), $id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $orders = $this->order_product_dao->count_by_product($id);
        if ($orders > 0) {
            throw new ValidationException("This product is part of $orders order(s) and cannot be deleted. Set is_available to 0 instead.");
        }

        $this->dao->begin_transaction();
        try {
            $this->product_category_dao->delete_by_product($id);
            $this->dao->delete($id);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
    }

    public function get_by_category($category_id)
    {
        Validator::integer($category_id, 'category_id', 1);
        if (!$this->category_dao->get_by_id($category_id)) {
            throw new NotFoundException("Category not found.");
        }
        return $this->dao->get_by_category($category_id);
    }

    protected function validate($product, $id = null)
    {
        Validator::required($product, ['name', 'price', 'stock_quantity']);
        Validator::length($product['name'], 'name', 2, 50);
        Validator::no_html($product['name'], 'name');
        if (!empty($product['description'])) {
            Validator::length($product['description'], 'description', 0, 500);
            Validator::no_html($product['description'], 'description');
        }
        Validator::number($product['price'], 'price', 0);
        Validator::integer($product['stock_quantity'], 'stock_quantity', 0);

        $existing = $this->dao->get_by_name(trim($product['name']));
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A product named '{$product['name']}' already exists.");
        }
    }

    private function normalize($product)
    {
        $product = $this->empty_to_null($product, ['description']);
        if (isset($product['name'])) {
            $product['name'] = trim($product['name']);
        }
        if (isset($product['is_available'])) {
            $product['is_available'] = filter_var($product['is_available'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        return $product;
    }
}
