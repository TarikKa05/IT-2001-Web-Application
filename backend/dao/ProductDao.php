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
        $sql = "SELECT p.*, GROUP_CONCAT(DISTINCT c.name) as category_names
                FROM {$this->table} p
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.id = :id
                GROUP BY p.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getAllProducts() {
        $sql = "SELECT p.*, GROUP_CONCAT(DISTINCT c.name) as category_names
                FROM {$this->table} p
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                GROUP BY p.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateProduct($id, $product) {
        return $this->update($id, $product);
    }

    public function deleteProduct($id) {
        return $this->delete($id);
    }

    public function getProductsByCategoryName($categoryName) {
        $sql = "SELECT p.*, GROUP_CONCAT(DISTINCT c.name) as category_names FROM " . $this->table . " p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                INNER JOIN categories c ON pc.category_id = c.id
                WHERE c.name = :category_name
                GROUP BY p.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':category_name', $categoryName);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
