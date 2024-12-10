<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Asset;
use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector;
use React\EventLoop\Loop;

class BinanceWebSocketService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function startWebSocket(): void
    {
        $loop = Loop::get();
        $connector = new Connector($loop);

        $connector('wss://stream.binance.com:9443/ws/btcusdt@bookTicker')
            ->then(function (WebSocket $conn) use ($loop) {
                $conn->on('message', function ($message) use ($conn) {
                    $data = json_decode($message, true);

                    if (isset($data['b']) && isset($data['a'])) {
                        $bid = $data['b']; // Bid rate
                        $ask = $data['a']; // Ask rate
                        $this->updateAsset('BTC/USD', $bid, $ask);
                    }
                });

                $conn->on('close', function () {
                    echo "WebSocket connection closed.\n";
                });
            }, function (\Exception $e) {
                echo "WebSocket connection error: {$e->getMessage()}\n";
            });

        $loop->run();
    }

    private function updateAsset(string $assetName, string $bid, string $ask): void
    {
        $assetRepo = $this->entityManager->getRepository(Asset::class);
        $asset = $assetRepo->findOneBy(['assetName' => $assetName]) ?? new Asset();

        $asset->setAssetName($assetName);
        $asset->setBid($bid);
        $asset->setAsk($ask);
        $asset->setDateUpdate(new \DateTime());

        $this->entityManager->persist($asset);
        $this->entityManager->flush();
    }
}
