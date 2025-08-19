<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/book/{id}', name: 'book_show', methods: ['GET', 'POST'])]
    public function show(int $id, BookRepository $books, Request $request, EntityManagerInterface $em): Response
    {
        $book = $books->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        $currentUser = $this->getUser();
        $isOwner = $currentUser && $book->getUser() === $currentUser;

        if ($request->isMethod('POST')) {
            $rating = (int) $request->request->get('rating', 0);
            $status = (string) $request->request->get('status', 'reading');

            $allowedStatuses = ['planned', 'reading', 'finished'];
            if ($status !== null && in_array($status, $allowedStatuses, true)) {
                $book->setStatus($status);
            }

            if ($book->getStatus() !== 'finished') {
                $book->setRating(null);
            } else {
                // Tylko jeśli status to "finished" i podano ocenę
                if ($rating > 0) {
                    if ($rating < 1 || $rating > 10) {
                        $this->addFlash('danger', 'Ocena musi być w zakresie 1–10.');
                        return $this->redirectToRoute('book_show', ['id' => $id]);
                    }
                    $book->setRating($rating);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Książka została zaktualizowana.');

            return $this->redirectToRoute('book_show', ['id' => $id]);
        }
        return $this->render('book/show.html.twig', [
            'book' => $book,
            'isOwner' => $isOwner,
        ]);
    }

    #[Route('/book/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, BookRepository $books, Request $request, EntityManagerInterface $em): Response
    {
        $book = $books->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        $currentUser = $this->getUser();
        if(!$currentUser || $book->getUser() !== $currentUser){
            $this->addFlash('danger', 'Nie masz uprawnień do edycji tej książki.');
            return $this->redirectToRoute('book_show', ['id' => $id]);
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
