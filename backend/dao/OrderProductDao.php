<?php
require_once 'BaseDao.php';

class OrderProductDao extends BaseDao {
    public function __construct() {
        parent::__construct("order_products");
    }

    public function createOrderProduct($orderProduct) {
        return $this->insert($orderProduct);
    }

    public function getOrderProductById($id) {
        return $this->getById($id);
    }

    public function getAllOrderProducts() {
        return $this->getAll();
    }

    public function updateOrderProduct($id, $orderProduct) {
        return $this->update($id, $orderProduct);
    }

    public function deleteOrderProduct($id) {
        return $this->delete($id);
    }
}
?>
