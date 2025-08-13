<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DbCheckController extends AbstractController
{
    #[Route('/db/check', name: 'app_db_check')]
    public function index(): Response
    {
        return $this->render('db_check/show.html.twig', [
            'controller_name' => 'DbCheckController',
        ]);
    }
}
