<?php
use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Gym Club API',
    description: 'REST API of Gym Club – supplement shop of a gym – IT 2001 Web Programming.',
    version: '1.0',
    contact: new OA\Contact(name: 'Tarik Korman')
)]
#[OA\Server(url: API_BASE_URL, description: 'This server')]
#[OA\Schema(
    schema: 'Error',
    properties: [
        new OA\Property(property: 'status', type: 'integer', example: 400),
        new OA\Property(property: 'error', type: 'string', example: 'Field \'name\' is required.')
    ]
)]
#[OA\Schema(
    schema: 'Message',
    properties: [new OA\Property(property: 'message', type: 'string', example: 'Product deleted.')]
)]
#[OA\Schema(
    schema: 'UserInput',
    required: ['name', 'username', 'email', 'phone_number', 'date_of_birth'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Lejla Begic'),
        new OA\Property(property: 'username', type: 'string', example: 'lejlab'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'lejla@gymclub.ba'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'Secret123', description: 'Required when a user is created. At least 8 characters, including a letter and a number.'),
        new OA\Property(property: 'phone_number', type: 'string', example: '062123456'),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '2002-04-15'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'customer'], example: 'customer')
    ]
)]
#[OA\Schema(
    schema: 'User',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'name', type: 'string', example: 'Tarik Korman'),
        new OA\Property(property: 'username', type: 'string', example: 'tarikk'),
        new OA\Property(property: 'email', type: 'string', example: 'tarik@gymclub.ba'),
        new OA\Property(property: 'phone_number', type: 'string', example: '060000000'),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', example: '2000-01-01'),
        new OA\Property(property: 'role', type: 'string', example: 'customer')
    ]
)]
#[OA\Schema(
    schema: 'CategoryInput',
    required: ['name'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Vitamins'),
        new OA\Property(property: 'description', type: 'string', example: 'Vitamins and minerals for every day.')
    ]
)]
#[OA\Schema(
    schema: 'Category',
    allOf: [new OA\Schema(ref: '#/components/schemas/CategoryInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 1)]
)]
#[OA\Schema(
    schema: 'CategoryStats',
    properties: [
        new OA\Property(property: 'category', type: 'string', example: 'Protein'),
        new OA\Property(property: 'total', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'ProductInput',
    required: ['name', 'price', 'stock_quantity'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'BCAA'),
        new OA\Property(property: 'description', type: 'string', example: 'Amino acids for faster recovery.'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 25.5),
        new OA\Property(property: 'stock_quantity', type: 'integer', example: 20),
        new OA\Property(property: 'is_available', type: 'boolean', example: true)
    ]
)]
#[OA\Schema(
    schema: 'Product',
    allOf: [new OA\Schema(ref: '#/components/schemas/ProductInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 1)]
)]
#[OA\Schema(
    schema: 'ProductCategoryInput',
    required: ['product_id', 'category_id'],
    properties: [
        new OA\Property(property: 'product_id', type: 'integer', example: 2),
        new OA\Property(property: 'category_id', type: 'integer', example: 4)
    ]
)]
#[OA\Schema(
    schema: 'ProductCategory',
    allOf: [new OA\Schema(ref: '#/components/schemas/ProductCategoryInput')],
    properties: [new OA\Property(property: 'id', type: 'integer', example: 1)]
)]
#[OA\Schema(
    schema: 'OrderInput',
    required: ['user_id'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 3),
        new OA\Property(property: 'order_date', type: 'string', example: '2026-11-20 12:00:00', description: 'Optional. The current date and time is used when it is missing.')
    ]
)]
#[OA\Schema(
    schema: 'Order',
    allOf: [new OA\Schema(ref: '#/components/schemas/OrderInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 70.00, description: 'Calculated by the server from the products of the order.')
    ]
)]
#[OA\Schema(
    schema: 'OrderWithProducts',
    allOf: [new OA\Schema(ref: '#/components/schemas/Order')],
    properties: [
        new OA\Property(property: 'products', type: 'array', items: new OA\Items(ref: '#/components/schemas/OrderProduct'))
    ]
)]
#[OA\Schema(
    schema: 'PlaceOrderInput',
    required: ['user_id', 'items'],
    properties: [
        new OA\Property(property: 'user_id', type: 'integer', example: 3),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(
                required: ['product_id', 'quantity'],
                properties: [
                    new OA\Property(property: 'product_id', type: 'integer', example: 1),
                    new OA\Property(property: 'quantity', type: 'integer', example: 2)
                ]
            )
        )
    ]
)]
#[OA\Schema(
    schema: 'OrderProductInput',
    required: ['order_id', 'product_id', 'quantity'],
    properties: [
        new OA\Property(property: 'order_id', type: 'integer', example: 3),
        new OA\Property(property: 'product_id', type: 'integer', example: 2),
        new OA\Property(property: 'quantity', type: 'integer', example: 1)
    ]
)]
#[OA\Schema(
    schema: 'OrderProductQuantity',
    required: ['quantity'],
    properties: [new OA\Property(property: 'quantity', type: 'integer', example: 2)]
)]
#[OA\Schema(
    schema: 'OrderProduct',
    allOf: [new OA\Schema(ref: '#/components/schemas/OrderProductInput')],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'unit_price', type: 'number', format: 'float', example: 30.00, description: 'Taken from the price of the product.'),
        new OA\Property(property: 'product_name', type: 'string', example: 'CREATINE')
    ]
)]
#[OA\Schema(
    schema: 'Weather',
    properties: [
        new OA\Property(property: 'city', type: 'string', example: 'Sarajevo'),
        new OA\Property(property: 'country', type: 'string', example: 'BA'),
        new OA\Property(property: 'temperature', type: 'number', format: 'float', example: 12.95),
        new OA\Property(property: 'feels_like', type: 'number', format: 'float', example: 12.05),
        new OA\Property(property: 'description', type: 'string', example: 'broken clouds'),
        new OA\Property(property: 'humidity', type: 'integer', example: 67),
        new OA\Property(property: 'wind_speed', type: 'number', format: 'float', example: 2.06)
    ]
)]
class OpenApiSpec {}
