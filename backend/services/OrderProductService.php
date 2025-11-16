<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderProductDao.php';
class OrderProductService extends BaseService
{
    public function __construct()
    {

        $dao = new OrderProductDao();
        parent::__construct($dao);
    }

    public function createOrderProduct($orderProduct)
    {
        if (empty($orderProduct['order_id']) || empty($orderProduct['product_id']) || empty($orderProduct['quantity']) || empty($orderProduct['unit_price'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!is_numeric($orderProduct['quantity']) || $orderProduct['quantity'] <= 0) {
            throw new InvalidArgumentException('quantity must be a positive number.');
        }

        if (!is_numeric($orderProduct['unit_price']) || $orderProduct['unit_price'] < 0) {
            throw new InvalidArgumentException('unit_price must be a non-negative number.');
        }

        return $this->create($orderProduct);
    }
    public function getOrderProductById($id)
    {
        return $this->getById($id);
    }
    public function getAllOrderProducts()
    {
        return $this->getAll();
    }
    public function updateOrderProduct($id, $orderProduct)
    {
        if (empty($orderProduct['order_id']) || empty($orderProduct['product_id']) || empty($orderProduct['quantity']) || empty($orderProduct['unit_price'])) {
            throw new InvalidArgumentException('Field is required.');
        }

        if (!is_numeric($orderProduct['quantity']) || $orderProduct['quantity'] <= 0) {
            throw new InvalidArgumentException('quantity must be a positive number.');
        }

        if (!is_numeric($orderProduct['unit_price']) || $orderProduct['unit_price'] < 0) {
            throw new InvalidArgumentException('unit_price must be a non-negative number.');
        }

        return $this->update($id, $orderProduct);
    }
    public function deleteOrderProduct($id)
    {
        return $this->delete($id);
    }
}

?>