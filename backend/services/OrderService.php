<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
class OrderService extends BaseService {
   public function __construct() {

       $dao = new OrderDao();
       parent::__construct($dao);
   }

   public function createOrder($order) {
       return $this->create($order);
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
