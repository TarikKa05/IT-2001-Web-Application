<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderProductDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';

class OrderProductService extends BaseService
{
    protected $entity_name = 'Order product';
    private $order_dao;
    private $product_dao;

    public function __construct($dao = null, $order_dao = null, $product_dao = null)
    {
        parent::__construct($dao ?? new OrderProductDao());
        $this->order_dao = $order_dao ?? new OrderDao();
        $this->product_dao = $product_dao ?? new ProductDao();
    }

    public function add($order_product)
    {
        $this->validate($order_product);

        $product = $this->product_dao->get_by_id($order_product['product_id']);
        if (!$product['is_available']) {
            throw new ValidationException("'{$product['name']}' is not available at the moment.");
        }
        if ($product['stock_quantity'] < $order_product['quantity']) {
            throw new ValidationException("Only {$product['stock_quantity']} piece(s) of '{$product['name']}' left in stock.");
        }

        $order_product['unit_price'] = $product['price'];

        $this->dao->begin_transaction();
        try {
            $created = $this->dao->add($order_product);
            if ($this->product_dao->change_stock($product['id'], -$order_product['quantity']) === 0) {
                throw new ValidationException("Only {$product['stock_quantity']} piece(s) of '{$product['name']}' left in stock.");
            }
            $this->recalculate_total($order_product['order_id']);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }

        return $this->dao->get_by_id($created['id']);
    }

    public function update($order_product, $id)
    {
        $existing = $this->get_by_id($id);
        Validator::required($order_product, ['quantity']);
        Validator::integer($order_product['quantity'], 'quantity', 1);

        $product = $this->product_dao->get_by_id($existing['product_id']);
        $difference = $order_product['quantity'] - $existing['quantity'];

        $this->dao->begin_transaction();
        try {
            if ($difference !== 0 && $this->product_dao->change_stock($product['id'], -$difference) === 0) {
                throw new ValidationException("Only {$product['stock_quantity']} more piece(s) of '{$product['name']}' left in stock.");
            }
            $this->dao->update(['quantity' => $order_product['quantity']], $id);
            $this->recalculate_total($existing['order_id']);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }

        return $this->dao->get_by_id($id);
    }

    public function delete($id)
    {
        $existing = $this->get_by_id($id);

        $this->dao->begin_transaction();
        try {
            $this->dao->delete($id);
            $this->product_dao->change_stock($existing['product_id'], $existing['quantity']);
            $this->recalculate_total($existing['order_id']);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
    }

    public function get_by_order($order_id)
    {
        Validator::integer($order_id, 'order_id', 1);
        if (!$this->order_dao->get_by_id($order_id)) {
            throw new NotFoundException("Order not found.");
        }
        return $this->dao->get_by_order($order_id);
    }

    protected function validate($order_product, $id = null)
    {
        Validator::required($order_product, ['order_id', 'product_id', 'quantity']);
        Validator::integer($order_product['order_id'], 'order_id', 1);
        Validator::integer($order_product['product_id'], 'product_id', 1);
        Validator::integer($order_product['quantity'], 'quantity', 1);

        if (!$this->order_dao->get_by_id($order_product['order_id'])) {
            throw new ValidationException("The selected order does not exist.");
        }
        $product = $this->product_dao->get_by_id($order_product['product_id']);
        if (!$product) {
            throw new ValidationException("The selected product does not exist.");
        }

        $existing = $this->dao->get_by_order_and_product($order_product['order_id'], $order_product['product_id']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("'{$product['name']}' is already in this order. Change its quantity instead.");
        }
    }

    private function recalculate_total($order_id)
    {
        $total = 0;
        foreach ($this->dao->get_by_order($order_id) as $line) {
            $total += $line['unit_price'] * $line['quantity'];
        }
        $this->order_dao->update(['total_amount' => round($total, 2)], $order_id);
    }
}
