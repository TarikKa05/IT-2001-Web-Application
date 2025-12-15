<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
class OrderService extends BaseService
{
    public function __construct()
    {

        $dao = new OrderDao();
        parent::__construct($dao);
    }

    public function createOrder($order)
    {
        if (empty($order['user_id']) || empty($order['total_price']) || empty($order['status'])) {
            throw new InvalidArgumentException('Field is  required.');
        }

        if (!is_numeric($order['total_price']) || $order['total_price'] < 0) {
            throw new InvalidArgumentException('total_price must be a non-negative number.');
        }

        return $this->create($order);
    }

    public function getOrderById($id)
    {
        return $this->getById($id);
    }
    public function getAllOrders()
    {
        return $this->dao->getAllOrders();
    }
    public function updateOrder($id, $order)
    {
        if (empty($order['user_id']) || empty($order['total_price']) || empty($order['status'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!is_numeric($order['total_price']) || $order['total_price'] < 0) {
            throw new InvalidArgumentException('total_price must be a non-negative number.');
        }

        return $this->update($id, $order);
    }
    public function deleteOrder($id)
    {
        // Remove child order_products first to satisfy FK constraints
        $stmt = $this->dao->getConnection()->prepare("DELETE FROM order_products WHERE order_id = :id");
        $stmt->execute(['id' => $id]);
        return $this->delete($id);
    }
}

?>
