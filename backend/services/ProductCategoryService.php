<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';
class ProductCategoryService extends BaseService {
   public function __construct() {

       $dao = new ProductCategoryDao();
       parent::__construct($dao);
   }

   public function createProductCategory ($productCategory) {
       return $this->dao->createProductCategory($productCategory);
   }
   public function getProductCategoryById($id) {
       return $this->dao->getProductCategoryById($id);
   }
   public function  getAllProductCategories() {
       return $this->dao->getAllProductCategories();
   }
   public function  updateProductCategory($id, $productCategory) {
       return $this->dao->updateProductCategory($id, $productCategory);
   }
   public function deleteProductCategory($id) {
       return $this->dao->deleteProductCategory($id);
   }
}

?>
