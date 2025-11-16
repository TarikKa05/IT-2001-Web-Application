<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';
class ProductService extends BaseService {
   public function __construct() {

       $dao = new ProductDao();
       parent::__construct($dao);
   }

   public function createProduct($product) {
        if (empty($product['name']) || empty($product['description']) || empty($product['price']) || empty($product['stock_quantity']) || !isset($product['is_available'])) {
        throw new InvalidArgumentException('Field is required.');
    }

        if (!is_numeric($product['price']) || $product['price'] < 0) {
            throw new InvalidArgumentException('price must be a non-negative number.');
        }

        if (!is_numeric($product['stock_quantity']) || $product['stock_quantity'] < 0) {
            throw new InvalidArgumentException('stock_quantity must be a non-negative number.');
        }
        return $this->create($product);
   }
   public function getProductById($id) {
       return $this->getById($id);
   }
   public function getAllProducts() {
       return $this->getAll();
   }
   public function updateProduct($id, $product) {
       if (empty($product['name']) || empty($product['description']) || empty($product['price']) || empty($product['stock_quantity']) || !isset($product['is_available'])) {
        throw new InvalidArgumentException('Field is required.');
    }

        if (!is_numeric($product['price']) || $product['price'] < 0) {
            throw new InvalidArgumentException('price must be a non-negative number.');
        }

        if (!is_numeric($product['stock_quantity']) || $product['stock_quantity'] < 0) {
            throw new InvalidArgumentException('stock_quantity must be a non-negative number.');
        }

        return $this->update($id, $product);
   }
   public function deleteProduct($id) {
       return $this->delete($id);
   }
   public function getProductsByCategoryName($categoryName) {
       return $this->dao->getProductsByCategoryName($categoryName);
   }
}

?>
