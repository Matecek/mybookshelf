<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/book/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, BookRepository $books, Request $request, EntityManagerInterface $em): Response
    {
        $book = $books->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }
        if ($request->isMethod('POST')) {
            $title       = (string) $request->request->get('title', '');
            $author      = (string) $request->request->get('author', '');
            $description = $request->request->get('description') ?: null;
            $status      = (string) $request->request->get('status', 'reading');

            if ($title === '' || $author === '') {
                $this->addFlash('danger', 'Tytuł i autor są wymagane.');
                return $this->render('book/edit.html.twig', ['book' => $book]);
            }

            $book->setTitle($title);
            $book->setAuthor($author);
            if (method_exists($book, 'setDescription')) {
                $book->setDescription($description);
            }
            $book->setStatus($status);

            $em->flush();

            return $this->redirectToRoute('book_show', ['id' => $id]);
        }

        return $this->render('book/edit.html.twig', [
            'book' => $book,
        ]);
    }
}
