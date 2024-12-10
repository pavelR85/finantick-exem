<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiClient
{
    private $client;
    private $apiUrl;

    public function __construct(HttpClientInterface $client, string $apiUrl = 'https://api.binance.com/api/v3/ticker/bookTicker?symbol=BTCUSDT')
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
    }

    public function fetchData(): array
    {
        $response = $this->client->request('GET', $this->apiUrl);
        return $response->toArray();
    }
}
