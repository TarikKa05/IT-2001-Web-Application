<?php
require_once 'BaseService.php';
require_once '../dao/OrderProductDao.php';
class OrderProductService extends BaseService {
   public function __construct() {

       $dao = new OrderProductDao();
       parent::__construct($dao);
   }

   public function createOrderProduct($orderProduct) {
       return $this->dao->creatOrderProduct($orderProduct);
   }
   public function getOrderProductById($id) {
       return $this->dao->getOrderProductById($id);
   }
   public function  getAllOrderProducts() {
       return $this->dao->getAllOrderProducts();
   }
   public function updateOrderProduct($id, $orderProduct) {
       return $this->dao->updateOrderProduct($id, $orderProduct);
   }
   public function deleteOrderProduct($id) {
       return $this->dao->deleteOrderProduct($id);
   }
}

?>
