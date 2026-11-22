<?php
Flight::route('GET /weather/current', function () {
    $city = Flight::request()->query['city'] ?? '';
    Flight::json(Flight::weather_service()->get_current($city));
});
