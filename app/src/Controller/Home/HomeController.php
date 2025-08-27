<?php

namespace App\Controller\Home;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(BookRepository $books): Response
    {
        if (!$this->getUser()) {
            $data = [];
        } else {
            $data = $books->createQueryBuilder('b')
                ->leftJoin('b.user', 'u')
                ->addSelect('u')
                ->orderBy('b.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
        }

            return $this->render('homepage/index.html.twig', [
                'books' => $data,
            ]);
    }
}
