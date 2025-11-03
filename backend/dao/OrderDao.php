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
        return $this->getAll();
    }

    public function updateOrder($id, $order) {
        return $this->update($id, $order);
    }

    public function deleteOrder($id) {
        return $this->delete($id);
    }
}
?>
