<?php
use OpenApi\Attributes as OA;

class ProductPaths
{
    #[OA\Get(
        path: '/products',
        tags: ['products'],
        summary: 'List all products',
        responses: [
            new OA\Response(response: 200, description: 'All products',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')))
        ]
    )]
    public function getProducts() {}

    #[OA\Get(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Get one product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getProductsById() {}

    #[OA\Post(
        path: '/products',
        tags: ['products'],
        summary: 'Create a product',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid data or the name already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postProducts() {}

    #[OA\Put(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Update a product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated product', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid data or the name already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putProductsById() {}

    #[OA\Delete(
        path: '/products/{id}',
        tags: ['products'],
        summary: 'Delete a product',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The product is part of an order', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteProductsById() {}
}
