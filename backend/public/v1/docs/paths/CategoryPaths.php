<?php
use OpenApi\Attributes as OA;

class CategoryPaths
{
    #[OA\Get(
        path: '/categories',
        tags: ['categories'],
        summary: 'List all categories',
        responses: [
            new OA\Response(response: 200, description: 'All categories',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Category')))
        ]
    )]
    public function getCategories() {}

    #[OA\Get(
        path: '/categories/stats',
        tags: ['categories'],
        summary: 'Number of products in every category',
        responses: [
            new OA\Response(response: 200, description: 'One row per category',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/CategoryStats')))
        ]
    )]
    public function getCategoriesStats() {}

    #[OA\Get(
        path: '/categories/{id}',
        tags: ['categories'],
        summary: 'Get one category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCategoriesById() {}

    #[OA\Post(
        path: '/categories',
        tags: ['categories'],
        summary: 'Create a category',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        responses: [
            new OA\Response(response: 201, description: 'The new category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 400, description: 'Invalid data or the name already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function postCategories() {}

    #[OA\Put(
        path: '/categories/{id}',
        tags: ['categories'],
        summary: 'Update a category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CategoryInput')),
        responses: [
            new OA\Response(response: 200, description: 'The updated category', content: new OA\JsonContent(ref: '#/components/schemas/Category')),
            new OA\Response(response: 400, description: 'Invalid data or the name already exists', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function putCategoriesById() {}

    #[OA\Delete(
        path: '/categories/{id}',
        tags: ['categories'],
        summary: 'Delete a category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category deleted', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(response: 400, description: 'The category still contains products', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function deleteCategoriesById() {}

    #[OA\Get(
        path: '/categories/{id}/products',
        tags: ['categories', 'products'],
        summary: 'The products of one category',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, example: 1, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'The products in the category',
                content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product'))),
            new OA\Response(response: 404, description: 'Category not found', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getCategoriesByIdProducts() {}
}
