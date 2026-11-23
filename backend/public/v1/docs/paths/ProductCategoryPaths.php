<?php
use OpenApi\Attributes as OA;

class ProductCategoryPaths
{
    #[OA\Get(
        path: '/product-categories',
        tags: ['product-categories'],
        summary: 'List all product–category links',
        responses: [
            new OA\Response(response: 200, description: 'All links',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/ProductCategory')))
        ]
    )]
    public function getProductCategories() {}

    #[OA\Get(
        path: '/product-categories/{id}',
        tags: ['product-categories'],
        summary: 'Get one product–category link',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The link', content: new OA\JsonContent(ref: '#/components/schemas/ProductCategory')),
            new OA\Response(response: 404, description: 'Product category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getProductCategoriesById() {}

    #[OA\Post(
        path: '/product-categories',
        tags: ['product-categories'],
        summary: 'Put a product into a category',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductCategoryInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new link', content: new OA\JsonContent(ref: '#/components/schemas/ProductCategory')),
            new OA\Response(response: 400, description: 'The product or category does not exist, or the product is already in the category', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postProductCategories() {}

    #[OA\Put(
        path: '/product-categories/{id}',
        tags: ['product-categories'],
        summary: 'Change a product–category link',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProductCategoryInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated link', content: new OA\JsonContent(ref: '#/components/schemas/ProductCategory')),
            new OA\Response(response: 400, description: 'The product or category does not exist, or the product is already in the category', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Product category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putProductCategoriesById() {}

    #[OA\Delete(
        path: '/product-categories/{id}',
        tags: ['product-categories'],
        summary: 'Remove a product from a category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Product category deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 404, description: 'Product category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteProductCategoriesById() {}
}
