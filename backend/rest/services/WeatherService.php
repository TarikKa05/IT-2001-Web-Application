<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/exceptions.php';
require_once __DIR__ . '/../../utils/Validator.php';

class WeatherService
{
    private const CONNECT_TIMEOUT = 3;
    private const TIMEOUT = 5;
    private const UNAVAILABLE = 'The weather service is not available at the moment. Please try again later.';

    public function get_current($city)
    {
        $city = trim((string) $city);
        Validator::length($city, 'city', 2, 60);
        Validator::no_html($city, 'city');

        $url = Config::WEATHER_API_URL() . '?' . http_build_query([
            'q' => $city,
            'units' => 'metric',
            'appid' => Config::WEATHER_API_KEY()
        ]);

        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => self::CONNECT_TIMEOUT,
            CURLOPT_TIMEOUT => self::TIMEOUT
        ]);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($body === false) {
            error_log("Weather API request failed: $error");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }
        if ($status === 404) {
            throw new NotFoundException("No city called '$city' was found.");
        }
        if ($status !== 200) {
            error_log("Weather API answered with status $status: $body");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }

        $data = json_decode($body, true);
        if (!is_array($data) || !isset($data['main'])) {
            error_log("Weather API returned an unexpected answer: $body");
            throw new ExternalServiceException(self::UNAVAILABLE);
        }

        return [
            'city' => $data['name'] ?? $city,
            'country' => $data['sys']['country'] ?? null,
            'temperature' => $data['main']['temp'],
            'feels_like' => $data['main']['feels_like'] ?? null,
            'description' => $data['weather'][0]['description'] ?? null,
            'humidity' => $data['main']['humidity'] ?? null,
            'wind_speed' => $data['wind']['speed'] ?? null
        ];
    }
}
