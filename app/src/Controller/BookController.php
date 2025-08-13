<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(BookRepository $books): Response
    {
        $data = $books->createQueryBuilder('b')
            ->leftJoin('b.user', 'u')
            ->addSelect('u')
            ->orderBy('b.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('homepage/index.html.twig', [
            'books' => $data,
        ]);
    }
}
