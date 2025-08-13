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

        return $this->render('homepage/show.html.twig', [
            'books' => $data,
        ]);
    }

    #[Route('/book/{id}', name: 'book_show', methods: ['GET'])]
    public function show(int $id, BookRepository $books): Response
    {
        $book = $books->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }
}
