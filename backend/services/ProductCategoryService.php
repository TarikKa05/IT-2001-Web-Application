<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ProductCategoryDao.php';
class ProductCategoryService extends BaseService {
   public function __construct() {

       $dao = new ProductCategoryDao();
       parent::__construct($dao);
   }

   public function createProductCategory ($productCategory) {
        if (empty($productCategory['product_id']) || empty($productCategory['category_id'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!is_numeric($productCategory['product_id']) || !is_numeric($productCategory['category_id'])) {
            throw new InvalidArgumentException('product_id and category_id must be numbers.');
        }

        return $this->create($productCategory);
   }
   public function getProductCategoryById($id) {
       return $this->getById($id);
   }
   public function  getAllProductCategories() {
       return $this->getAll();
   }
   public function  updateProductCategory($id, $productCategory) {
        if (empty($productCategory['product_id']) || empty($productCategory['category_id'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!is_numeric($productCategory['product_id']) || !is_numeric($productCategory['category_id'])) {
            throw new InvalidArgumentException('product_id and category_id must be numbers.');
        }

        return $this->update($id, $productCategory);
   }
   public function deleteProductCategory($id) {
       return $this->delete($id);
   }
}

?>
