<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/OrderProductDao.php';
require_once __DIR__ . '/../dao/ProductDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class OrderService extends BaseService
{
    protected $entity_name = 'Order';
    private $order_product_dao;
    private $product_dao;
    private $user_dao;

    public function __construct($dao = null, $order_product_dao = null, $product_dao = null, $user_dao = null)
    {
        parent::__construct($dao ?? new OrderDao());
        $this->order_product_dao = $order_product_dao ?? new OrderProductDao();
        $this->product_dao = $product_dao ?? new ProductDao();
        $this->user_dao = $user_dao ?? new UserDao();
    }

    public function add($order)
    {
        $order['order_date'] = !empty($order['order_date']) ? $order['order_date'] : date('Y-m-d H:i:s');
        $order['total_amount'] = 0;
        return parent::add($order);
    }

    public function update($order, $id)
    {
        unset($order['total_amount']);
        return parent::update($order, $id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $lines = $this->order_product_dao->get_by_order($id);

        $this->dao->begin_transaction();
        try {
            foreach ($lines as $line) {
                $this->product_dao->change_stock($line['product_id'], $line['quantity']);
            }
            $this->order_product_dao->delete_by_order($id);
            $this->dao->delete($id);
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }
    }

    public function get_by_user($user_id)
    {
        Validator::integer($user_id, 'user_id', 1);
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new NotFoundException("User not found.");
        }
        return $this->dao->get_by_user($user_id);
    }

    public function get_with_products($id)
    {
        $order = $this->get_by_id($id);
        $order['products'] = $this->order_product_dao->get_by_order($id);
        return $order;
    }

    public function place_order($user_id, $items)
    {
        Validator::integer($user_id, 'user_id', 1);
        if (!$this->user_dao->get_by_id($user_id)) {
            throw new ValidationException("The selected user does not exist.");
        }
        if (!is_array($items) || count($items) === 0) {
            throw new ValidationException("An order needs at least one product.");
        }

        $lines = [];
        $total = 0;
        foreach ($items as $item) {
            Validator::required($item, ['product_id', 'quantity']);
            Validator::integer($item['product_id'], 'product_id', 1);
            Validator::integer($item['quantity'], 'quantity', 1);

            $product = $this->product_dao->get_by_id($item['product_id']);
            if (!$product) {
                throw new ValidationException("The selected product does not exist.");
            }
            if (isset($lines[$product['id']])) {
                throw new ValidationException("'{$product['name']}' is listed more than once. Change its quantity instead.");
            }
            if (!$product['is_available']) {
                throw new ValidationException("'{$product['name']}' is not available at the moment.");
            }
            if ($product['stock_quantity'] < $item['quantity']) {
                throw new ValidationException("Only {$product['stock_quantity']} piece(s) of '{$product['name']}' left in stock.");
            }

            $lines[$product['id']] = [
                'product_id' => $product['id'],
                'quantity' => (int) $item['quantity'],
                'unit_price' => $product['price']
            ];
            $total += $product['price'] * $item['quantity'];
        }

        $this->dao->begin_transaction();
        try {
            $order = $this->dao->add([
                'user_id' => $user_id,
                'total_amount' => round($total, 2),
                'order_date' => date('Y-m-d H:i:s')
            ]);
            foreach ($lines as $line) {
                $line['order_id'] = $order['id'];
                $this->order_product_dao->add($line);
                if ($this->product_dao->change_stock($line['product_id'], -$line['quantity']) === 0) {
                    throw new ValidationException("A product of this order has just run out of stock.");
                }
            }
            $this->dao->commit();
        } catch (Throwable $e) {
            $this->dao->rollback();
            throw $e;
        }

        return $this->get_with_products($order['id']);
    }

    protected function validate($order, $id = null)
    {
        Validator::required($order, ['user_id']);
        Validator::integer($order['user_id'], 'user_id', 1);
        if (!$this->user_dao->get_by_id($order['user_id'])) {
            throw new ValidationException("The selected user does not exist.");
        }
        if (!empty($order['order_date'])) {
            Validator::datetime($order['order_date'], 'order_date');
        }
    }
}
