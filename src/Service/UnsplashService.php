<?php

// src/Service/UnsplashService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class UnsplashService
{
    private $client;
    private $unsplashApiKey;

    public function __construct(HttpClientInterface $client, string $unsplashApiKey)
    {
        $this->client = $client;
        $this->unsplashApiKey = $unsplashApiKey;
    }

    public function searchImages(string $query, int $perPage = 10): array
    {
        $response = $this->client->request('GET', 'https://api.unsplash.com/search/photos', [
            'query' => [
                'query' => $query,
                'per_page' => $perPage,
                'client_id' => $this->unsplashApiKey,
            ],
        ]);

        $data = $response->toArray();

        return $data['results'] ?? [];
    }
}
