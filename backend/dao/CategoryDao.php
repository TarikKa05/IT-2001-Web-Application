<?php
require_once 'BaseDao.php';

class CategoryDao extends BaseDao {
    public function __construct() {
        parent::__construct("categories");
    }

    public function createCategory($category) {
        return $this->insert($category);
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

    public function getCategoryByName($name) {
        $query = "SELECT * FROM {$this->table} WHERE name = :name";
        return $this->query_unique($query, ['name' => $name]);
    }
}
?>
