<?php

declare(strict_types=1);

namespace App\Controller\Home;

use App\Services\Home\ShowBook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(ShowBook $showBook): Response
    {
        $books = $showBook->getBooksForHomepage($this->getUser());

        return $this->render('homepage/index.html.twig', [
            'books' => $books,
        ]);
    }
}