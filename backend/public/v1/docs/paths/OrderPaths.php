<?php
use OpenApi\Attributes as OA;

class OrderPaths
{
    #[OA\Get(
        path: '/orders',
        tags: ['orders'],
        summary: 'List all orders',
        responses: [
            new OA\Response(response: 200, description: 'All orders',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Order')))
        ]
    )]
    public function getOrders() {}

    #[OA\Get(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Get one order with its products',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The order and its products', content: new OA\JsonContent(ref: '#/components/schemas/OrderWithProducts')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrdersById() {}

    #[OA\Post(
        path: '/orders',
        tags: ['orders'],
        summary: 'Create an empty order (total 0, products are added with POST /order-products)',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new order', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 400, description: 'Invalid data or the user does not exist', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postOrders() {}

    #[OA\Post(
        path: '/orders/place',
        tags: ['orders'],
        summary: 'Place an order with its products in one step',
        description: 'Prices are taken from the database, the stock is checked and lowered, and the order and its products are saved in one transaction.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/PlaceOrderInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new order and its products', content: new OA\JsonContent(ref: '#/components/schemas/OrderWithProducts')),
            new OA\Response(response: 400, description: 'Invalid data, a product does not exist or is not available, or not enough stock', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postOrdersPlace() {}

    #[OA\Put(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Update an order (total_amount is always calculated by the server)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated order', content: new OA\JsonContent(ref: '#/components/schemas/Order')),
            new OA\Response(response: 400, description: 'Invalid data or the user does not exist', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putOrdersById() {}

    #[OA\Delete(
        path: '/orders/{id}',
        tags: ['orders'],
        summary: 'Delete an order (its products go back to stock)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteOrdersById() {}

    #[OA\Get(
        path: '/orders/{id}/products',
        tags: ['orders', 'order-products'],
        summary: 'The products of one order',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The products of the order with their names',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderProduct'))),
            new OA\Response(response: 404, description: 'Order not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrdersByIdProducts() {}
}
