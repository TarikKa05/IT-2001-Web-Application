<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductDao.php';
class ProductService extends BaseService {
   public function __construct() {

       $dao = new ProductDao();
       parent::__construct($dao);
   }

   public function createProduct($product) {
       return $this->create($product);
   }
   public function getProductById($id) {
       return $this->getById($id);
   }
   public function getAllProducts() {
       return $this->getAll();
   }
   public function updateProduct($id, $product) {
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
