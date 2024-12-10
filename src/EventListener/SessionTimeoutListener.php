<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class SessionTimeoutListener
{
    private $requestStack;
    private $timeoutDuration;
    private RouterInterface $router;

    public function __construct(RouterInterface $router, RequestStack $requestStack, int $timeoutDuration = 600) // 10 minutes
    {
        $this->requestStack = $requestStack;
        $this->timeoutDuration = $timeoutDuration;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        // Ignore logout routes
        if ($request->attributes->get('_route') === 'app_logout' ||
            $request->attributes->get('_route') === 'app_login' ||
            $request->attributes->get('_route') === 'main_page' ||
            $request->attributes->get('_route') === 'register') {
            return;
        }

        $session = $this->requestStack->getSession();

        if (!$session->isStarted()) {
            $session->start();
        }

        // Get the last activity timestamp
        $lastActivity = $session->get('last_activity');

        // If session expired
        if ($lastActivity && (time() - $lastActivity > $this->timeoutDuration)) {
            $session->invalidate();
            //return;
            $event->setResponse(new RedirectResponse($this->router->generate('main_page')));
        }

        // Update the last activity timestamp
        $session->set('last_activity', time());
    }
}
