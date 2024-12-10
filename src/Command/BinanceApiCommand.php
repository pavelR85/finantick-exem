<?php

namespace App\Command;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use App\Service\WebSocketClient;

#[AsCommand(
    name: 'app:fetch-binance-data',
    description: 'Fetch BTC/USD bid and ask rates from Binance API.',
    hidden: false,
    aliases: ['app:fetch-binance-data']
)]
class BinanceApiCommand extends Command
{
    protected static $defaultName = 'app:fetch-binance-data';
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $httpClient;
    private WebSocketClient $webSocketClient;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $httpClient, WebSocketClient $webSocketClient)
    {
        $this->entityManager = $entityManager;
        $this->httpClient = $httpClient;
        $this->webSocketClient = $webSocketClient;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Fetch BTC/USD bid and ask rates from Binance API.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->webSocketClient->connect();
        if($data and isset($data['b']) and isset($data['a'])) {
            $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['assetName' => 'BTC/USD']) ?? new Asset();
            $asset->setBid($data['b']);
            $asset->setAsk($data['a']);
            $asset->setDateUpdate(new \DateTime());
            $asset->setLotSize(10);

            $this->entityManager->persist($asset);
            $this->entityManager->flush();

            $output->writeln('Updated BTC/USD rates.');
        } else {
            $output->writeln('Failed to fetch data from Binance API.');
        }

        return Command::SUCCESS;
    }
}
