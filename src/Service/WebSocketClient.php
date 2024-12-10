<?php

namespace App\Service;

use WebSocket\Client;

class WebSocketClient
{
    private $wsUrl;

    public function __construct($wsUrl = 'wss://stream.binance.com:9443/ws/btcusdt@bookTicker')
    {
        $this->wsUrl = $wsUrl;
    }

    public function connect()
    {
        $client = new Client($this->wsUrl);
        $data = $client->receive();
        $client->close();
        return json_decode($data, true);
    }
}
