<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
class CategoryService extends BaseService {
   public function __construct() {

       $dao = new CategoryDao();
       parent::__construct($dao);
   }

   public function createCategory($category) {
       return $this->create($category);
   }
   public function getCategoryById($id) {
       return $this->getById($id);
   }
   public function getAllCategories() {
       return $this->getAll();
   }
   public function updateCategory($id, $category) {
       return $this->update($id, $category);
   }
   public function deleteCategory($id) {
       return $this->delete($id);
   }
}

?>
