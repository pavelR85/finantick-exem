<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Service\WebSocketClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetsController extends AbstractController
{

    #[Route('/update-assets', name: 'update_assets')]
    public function fetchAndSave(WebSocketClient $webSocketClient, EntityManagerInterface $em)
    {
        $data = $webSocketClient->connect();
        $asset = $em->getRepository(Asset::class)->findOneBy(['assetName' => 'BTC/USD']);
        if($data and isset($data['b']) and isset($data['a'])) {
            $asset->setBid($data['b']);
            $asset->setAsk($data['a']);
            $asset->setDateUpdate(new \DateTime());

            $em->persist($asset);
            $em->flush();
        }
        return $this->json(['bid' => $data['b'],'ask'=>$data['a']]);
    }

}
