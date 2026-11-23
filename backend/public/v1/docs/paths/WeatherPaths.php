<?php
use OpenApi\Attributes as OA;

class WeatherPaths
{
    #[OA\Get(
        path: '/weather/current',
        tags: ['weather'],
        summary: 'Current weather for a city (OpenWeatherMap)',
        parameters: [
            new OA\Parameter(
                name: 'city',
                in: 'query',
                required: true,
                description: 'City name',
                example: 'Sarajevo',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Current weather', content: new OA\JsonContent(ref: '#/components/schemas/Weather')),
            new OA\Response(response: 400, description: 'City name missing, too short or invalid', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 404, description: 'Unknown city', content: new OA\JsonContent(ref: '#/components/schemas/Error')),
            new OA\Response(response: 503, description: 'OpenWeatherMap not available (timeout or error)', content: new OA\JsonContent(ref: '#/components/schemas/Error'))
        ]
    )]
    public function getWeatherCurrent() {}
}
