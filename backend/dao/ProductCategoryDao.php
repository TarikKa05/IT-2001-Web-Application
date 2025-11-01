<?php
require_once 'BaseDao.php';

class ProductCategoryDao extends BaseDao {
    public function __construct() {
        parent::__construct("product_categories");
    }

    public function createProductCategory($productCategory) {
        return $this->insert($productCategory);
    }

    public function getProductCategoryById($id) {
        return $this->getById($id);
    }

    public function getAllProductCategories() {
        return $this->getAll();
    }

    public function updateProductCategory($id, $productCategory) {
        return $this->update($id, $productCategory);
    }

    public function deleteProductCategory($id) {
        return $this->delete($id);
    }


}
?>
