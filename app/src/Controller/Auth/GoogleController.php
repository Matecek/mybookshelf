<?php
declare(strict_types=1);
namespace App\Controller\Auth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GoogleController extends AbstractController
{
    #[Route('/login/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route('/login/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        // This will be handled by the authenticator
    }
}