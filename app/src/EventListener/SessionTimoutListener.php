<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionTimeoutListener
{
    private const TIMEOUT = 1300;

    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        if (!$session instanceof SessionInterface) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser()) {
            return;
        }

        $lastActivity = $session->get('last_activity');
        $now = time();

        if ($lastActivity && ($now - $lastActivity) > self::TIMEOUT) {
            $this->tokenStorage->setToken(null);
            $session->invalidate();

            $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
            $event->setResponse($response);
            return;
        }

        $session->set('last_activity', $now);
    }
}