<?php
require_once 'BaseDao.php';

class ProductDao extends BaseDao {
    public function __construct() {
        parent::__construct("products");
    }

    public function createProduct($product) {
        return $this->insert($product);
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
        $sql = "SELECT p.* FROM " . $this->table . " p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                INNER JOIN categories c ON pc.category_id = c.id
                WHERE c.name = :category_name";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':category_name', $categoryName);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
