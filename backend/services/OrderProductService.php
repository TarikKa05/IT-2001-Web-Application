<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderProductDao.php';
class OrderProductService extends BaseService {
   public function __construct() {

       $dao = new OrderProductDao();
       parent::__construct($dao);
   }

   public function createOrderProduct($orderProduct) {
       return $this->create($orderProduct);
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
