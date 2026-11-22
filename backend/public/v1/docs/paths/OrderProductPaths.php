<?php
use OpenApi\Attributes as OA;

class OrderProductPaths
{
    #[OA\Get(
        path: '/order-products',
        tags: ['order-products'],
        summary: 'List all order products',
        responses: [
            new OA\Response(response: 200, description: 'All order products',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderProduct')))
        ]
    )]
    public function getOrderProducts() {}

    #[OA\Get(
        path: '/order-products/{id}',
        tags: ['order-products'],
        summary: 'Get one order product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The order product', content: new OA\JsonContent(ref: '#/components/schemas/OrderProduct')),
            new OA\Response(response: 404, description: 'Order product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getOrderProductsById() {}

    #[OA\Post(
        path: '/order-products',
        tags: ['order-products'],
        summary: 'Add a product to an order',
        description: 'The unit price is taken from the product, the stock is lowered and the order total is recalculated.',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderProductInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new order product', content: new OA\JsonContent(ref: '#/components/schemas/OrderProduct')),
            new OA\Response(response: 400, description: 'Invalid data, the order or product does not exist, the product is already in the order, or not enough stock', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postOrderProducts() {}

    #[OA\Put(
        path: '/order-products/{id}',
        tags: ['order-products'],
        summary: 'Change the quantity of an order product',
        description: 'The stock and the order total change with the quantity.',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/OrderProductQuantity')),
        responses: [
            new OA\Response(response: 200, description: 'The updated order product', content: new OA\JsonContent(ref: '#/components/schemas/OrderProduct')),
            new OA\Response(response: 400, description: 'Invalid quantity or not enough stock', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Order product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putOrderProductsById() {}

    #[OA\Delete(
        path: '/order-products/{id}',
        tags: ['order-products'],
        summary: 'Remove a product from an order (it goes back to stock)',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Order product deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Order product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteOrderProductsById() {}
}
