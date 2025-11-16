<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
class CategoryService extends BaseService {
   public function __construct() {

       $dao = new CategoryDao();
       parent::__construct($dao);
   }

   public function createCategory($category) {
        if (empty($category['name'])) {
            throw new InvalidArgumentException('Field "name" is required.');
        }

        if ($this->dao->getCategoryByName($category['name'])) {
            throw new InvalidArgumentException('Category name already exists.');
        }
        return $this->create($category);
   }
   public function getCategoryById($id) {
       return $this->getById($id);
   }
   public function getAllCategories() {
       return $this->getAll();
   }
   public function updateCategory($id, $category) {
        if (empty($category['name'])) {
            throw new InvalidArgumentException('Field "name" is required.');
        }
        
        $existing = $this->dao->getCategoryByName($category['name']);
        if ($existing && $existing['id'] != $id) {
            throw new InvalidArgumentException('Category name already exists.');
        }
        return $this->update($id, $category);
   }
   public function deleteCategory($id) {
       return $this->delete($id);
   }
}

?>
