<?php
require_once 'BaseService.php';
require_once '../dao/ProductDao.php';
class ProductService extends BaseService {
   public function __construct() {

       $dao = new ProductDao();
       parent::__construct($dao);
   }

   public function createProduct($product) {
       return $this->dao->createProduct($product);
   }
   public function getProductById($id) {
       return $this->dao->getProductById($id);
   }
   public function getAllProducts() {
       return $this->dao->getAllProducts();
   }
   public function updateProduct($id, $product) {
       return $this->dao->updateProduct($id, $product);
   }
   public function deleteProduct($id) {
       return $this->dao->deleteProduct($id);
   }
   public function getProductsByCategoryName($categoryName) {
       return $this->dao->getProductsByCategoryName($categoryName);
   }
}

?>
