<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
class CategoryService extends BaseService {
   public function __construct() {

       $dao = new CategoryDao();
       parent::__construct($dao);
   }

   public function createCategory($category) {
       return $this->dao->createCategory($category);
   }
   public function getCategoryById($id) {
       return $this->dao->getCategoryById($id);
   }
   public function getAllCategories() {
       return $this->dao->getAllCategories();
   }
   public function updateCategory($id, $category) {
       return $this->dao->updateCategory($id, $category);
   }
   public function deleteCategory($id) {
       return $this->dao->deleteCategory($id);
   }
}

?>
