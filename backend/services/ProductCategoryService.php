<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';
class ProductCategoryService extends BaseService {
   public function __construct() {

       $dao = new ProductCategoryDao();
       parent::__construct($dao);
   }

   public function createProductCategory ($productCategory) {
       return $this->create($productCategory);
   }
   public function getProductCategoryById($id) {
       return $this->getById($id);
   }
   public function  getAllProductCategories() {
       return $this->getAll();
   }
   public function  updateProductCategory($id, $productCategory) {
       return $this->update($id, $productCategory);
   }
   public function deleteProductCategory($id) {
       return $this->delete($id);
   }
}

?>
