<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(BookRepository $books): Response
    {
        $user = $this->getUser();
        $book = $books->findBy(['user' => $user]);

        return $this->render('profile/index.html.twig', [
            'books' => $book,
        ]);
    }
}