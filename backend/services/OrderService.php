<?php
require_once 'BaseService.php';
require_once '../dao/OrderDao.php';
class OrderService extends BaseService {
   public function __construct() {

       $dao = new OrderDao();
       parent::__construct($dao);
   }

   public function createOrder($order) {
       return $this->dao->createOrder($order);
   }
   public function getOrderById($id) {
       return $this->dao->getOrderById($id);
   }
   public function getAllOrders() {
       return $this->dao->getAllOrders();
   }
   public function updateOrder($id, $order) {
       return $this->dao->updateOrder($id, $order);
   }
   public function deleteOrder($id) {
       return $this->dao->deleteOrder($id);
   }
}

?>
