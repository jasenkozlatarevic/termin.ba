<?php

class WeatherService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openweathermap.org/data/2.5/weather';

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getCurrentWeather(string $city): array
    {
        if (empty(trim($city))) {
            throw new InvalidArgumentException('City is required.');
        }

        $url = $this->baseUrl . '?' . http_build_query([
            'q' => $city,
            'appid' => $this->apiKey,
            'units' => 'metric',
        ]);

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException(
                'Weather service connection failed: ' . $curlError
            );
        }

        $data = json_decode($response, true);

if ($httpCode !== 200) {
    $message = $data['message'] ?? 'Unknown weather API error.';

    error_log("OpenWeather HTTP status: " . $httpCode);
    error_log("OpenWeather error message: " . $message);

    throw new RuntimeException(
        'OpenWeather HTTP ' . $httpCode . ': ' . $message,
        $httpCode
    );
}

        if (!isset($data['main'], $data['weather'][0])) {
            throw new RuntimeException('Invalid response from weather API.');
        }

        return [
            'city' => $data['name'],
            'country' => $data['sys']['country'] ?? null,
            'temperature' => $data['main']['temp'],
            'feels_like' => $data['main']['feels_like'],
            'humidity' => $data['main']['humidity'],
            'description' => $data['weather'][0]['description'],
            'icon' => $data['weather'][0]['icon'],
        ];
    }
}
