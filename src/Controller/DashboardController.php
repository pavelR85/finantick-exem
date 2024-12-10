<?php

namespace App\Controller;

use App\Entity\Asset;
use App\Entity\Trade;
use App\Service\WebSocketClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;
use App\Entity\Agent;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractController
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface  $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        // Get the current logged-in user
        $user = $this->tokenStorage->getToken()?->getUser();

        // Check if the user is logged in
        if ($user) {
            // If the user is an agent, show the agent dashboard
            if ($user instanceof Agent) {
                $unsignedUsers = $this->entityManager->getRepository(User::class)->findBy(['agent'=>null]);
                $unsignedAgents = $this->entityManager->getRepository(Agent::class)->createQueryBuilder('a')->innerJoin('a.subAgents', 'subAgent')
                ->where('subAgent IS NULL')->andWhere('a.id > 1')->getQuery()->getResult();
                //var_dump($unsignedAgents);die;
                return $this->render('dashboard/agent_dashboard.html.twig', [
                    'user' => $user,
                    'users' => $unsignedUsers,
                    'agents' => $unsignedAgents
                ]);
            }
            $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['assetName' => 'BTC/USD']);
            // If the user is a regular user, show the user dashboard
            return $this->render('dashboard/user_dashboard.html.twig', [
                'user' => $user,
                'asset' => $asset
            ]);
        }else {

            // Redirect to login if no user is found
            return $this->redirectToRoute('main_page');
        }
    }

    #[Route('/user/{id}/trades', name: 'user_trades')]
    public function viewUserTrades(int $id): Response
    {
        if($user = $this->tokenStorage->getToken()?->getUser()->getRoles()[0] == 'ROLE_USER'){
            return $this->redirectToRoute('dashboard');
        }
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
        $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['assetName' => 'BTC/USD']);
        return $this->render('dashboard/user_trades.html.twig', [
            'user' => $user,
            'asset' => $asset
        ]);
    }

    #[Route('/agent/{id}/assign-user', name: 'assign_user_to_agent')]
    public function assignUserToAgent(int $id, Request $request): Response
    {
        $agent = $this->entityManager->getRepository(Agent::class)->find($id);
        if (!$agent) {
            throw $this->createNotFoundException('Agent not found');
        }

        // Implement agent logic to assign users to the agent
        // Logic for assigning users can go here.

        return $this->render('dashboard/assign_user.html.twig', [
            'agent' => $agent
        ]);
    }

    #[Route('/user/open/trade', name: 'user_open_trade')]
    public function openTrade(WebSocketClient $webSocketClient, Request $request){
        $user = $this->tokenStorage->getToken()?->getUser();
        $agent = false;
        $post = json_decode($request->getContent(), true);
        if($user->getRoles()[0] != 'ROLE_USER'){
            $agent = $user;
            $user = $this->entityManager->getRepository(User::class)->find($post['user_id']);
        }
        $asset = $this->entityManager->getRepository(Asset::class)->findOneBy(['assetName' => 'BTC/USD']);
        $position = $post['position'];
        $lotCount = $post['lotCount'];
        $errorMess = '';
        if($position and $lotCount) {
            $askPrice = $asset->getAsk();
            $bidPrice = $asset->getBid();
            $entryRate = $position === 'buy' ? $askPrice : $bidPrice;
            $userCurrency = $user->getCurrency();//'EUR'; // User's currency
            // USD to EUR
            $conversionRateUSD = ($userCurrency == 'EUR') ? 0.9215 : 1;

            $lotSize = 10; // Constant
            $tradeSize = $lotSize * $lotCount;

            // Pip value calculation in user currency
            $pipValue = $tradeSize * 0.01 * $conversionRateUSD;

            $pnl = ($position == 'buy') ? ($bidPrice - $askPrice) * $pipValue * 100 : ($askPrice - $bidPrice) * $pipValue + 100;

            // Used margin calculation in user currency
            $usedMargin = $tradeSize * 0.1 * $conversionRateUSD * $bidPrice;

            $payout = 0;
            //save trade
            $trade = new Trade();
            $trade->setAgent($agent);
            $trade->setUser($user);
            $trade->setTradeSize($tradeSize);
            $trade->setPnl($pnl);
            $trade->setLotCount($lotCount);
            $trade->setEntryRate($entryRate);
            $trade->setPosition($position);
            $trade->setUsedMargin($usedMargin);
            $trade->setStatus('open');
            $trade->setDateCreated(new \DateTime());
            $trade->setPayout($payout);
            $this->entityManager->persist($trade);
            $this->entityManager->flush();
            // check if need to close trade
            if(($position == 'buy' and ($bidPrice >= $post['tp'] or $bidPrice <= $post['sl'])) or
                ($position == 'sell' and ($bidPrice >= $post['sl'] or $bidPrice <= $post['tp']))){
                if ($position == 'buy' and ($bidPrice >= $post['tp'] or $bidPrice <= $post['sl'])) {
                    $errorMess = ($bidPrice >= $post['tp']) ? 'reach TP' : 'reach SL';
                }
                if ($position == 'sell' and ($bidPrice >= $post['sl'] or $bidPrice <= $post['tp'])) {
                    $errorMess = ($bidPrice >= $post['sl']) ? 'reach SL' : 'reach TP';
                }
                $trade = $this->closeUpdateTrade($webSocketClient, $trade);
            }
        }

        if(!empty($errorMess)) {
            return $this->json(['reload' => 0,'error' => $errorMess]);
        }else{
            return $this->json(['reload' => 1]);
        }
    }

    public function closeUpdateTrade($webSocketClient, $trade){

        $data = $webSocketClient->connect();
        $conversionRateUSD = ($trade->getUser()->getCurrency() == 'EUR') ? 0.9215 : 1;
        $closeRate = $trade->getPosition() === 'buy' ? $data['b'] : $data['a'];
        $pipValue = $trade->getTradeSize() * 0.01 * $conversionRateUSD;
        if ($trade->getPosition() === 'buy') {
            $pnl = ($closeRate - $trade->getEntryRate()) * $pipValue * 100;
            $payout = ($closeRate - $trade->getEntryRate()) * $trade->getTradeSize() * $trade->getLotCount() * $pipValue;
        } else { // sell
            $pnl = ($trade->getEntryRate() - $closeRate) * $pipValue * 100;
            $payout = ($trade->getEntryRate() - $closeRate) * $trade->getTradeSize() * $trade->getLotCount() * $pipValue;
        }
        $trade->setPnl($pnl);
        $trade->setPayout($payout);
        $trade->setDateClose(new \DateTime());
        $trade->setStatus('close');
        $this->entityManager->persist($trade);
        $this->entityManager->flush();
        return $trade;
    }


    #[Route('/user/close/trade/{tradeId}', name: 'close_trade')]
    public function closeTrade(WebSocketClient $webSocketClient, Request $request, int $tradeId){
        $trade = $this->entityManager->getRepository(Trade::class)->find($tradeId);
        $trade = $this->closeUpdateTrade($webSocketClient, $trade);

        $referrer = $request->headers->get('referer');

        // Fallback if there's no referrer
        $url = $referrer ?: $this->generateUrl('dashboard');

        return new RedirectResponse($url);
    }
}
