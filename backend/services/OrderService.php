<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
class OrderService extends BaseService {
   public function __construct() {

       $dao = new OrderDao();
       parent::__construct($dao);
   }

   public function createOrder($order) {
        if ( empty($order['user_id']) || empty($order['total_price']) || empty($order['status'])) {
            throw new InvalidArgumentException('Field is  required.');
        }

        if (!is_numeric($order['total_price']) || $order['total_price'] < 0) {
            throw new InvalidArgumentException('total_price must be a non-negative number.');
        }

        return $this->create($order);
   }

   public function getOrderById($id) {
       return $this->getById($id);
   }
   public function getAllOrders() {
       return $this->getAll();
   }
   public function updateOrder($id, $order) {
        if ( empty($order['user_id']) || empty($order['total_price']) || empty($order['status'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!is_numeric($order['total_price']) || $order['total_price'] < 0) {
            throw new InvalidArgumentException('total_price must be a non-negative number.');
        }

        return $this->update($id, $order);
   }
   public function deleteOrder($id) {
       return $this->delete($id);
   }
}

?>
