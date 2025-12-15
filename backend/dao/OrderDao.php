<?php
require_once 'BaseDao.php';

class OrderDao extends BaseDao {
    public function __construct() {
        parent::__construct("orders");
    }

    public function createOrder($order) {
        return $this->insert($order);
    }

    public function getOrderById($id) {
        return $this->getById($id);
    }

    public function getAllOrders() {
        $sql = "SELECT o.*, u.email as user_email
                FROM {$this->table} o
                LEFT JOIN users u ON o.user_id = u.id";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateOrder($id, $order) {
        return $this->update($id, $order);
    }

    public function deleteOrder($id) {
        return $this->delete($id);
    }
}
?>
