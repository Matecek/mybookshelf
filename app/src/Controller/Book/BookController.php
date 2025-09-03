<?php
declare(strict_types=1);

namespace App\Controller\Book;

use App\Repository\BookRepository;
use App\Services\Book\EditBook;
use App\Services\Book\UpdateBookStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookController extends AbstractController
{
    public function __construct(private UpdateBookStatus $UpdateBookStatus){}

    #[Route('/book/{id}', name: 'book_show', methods: ['GET', 'POST'])]
    public function show(int $id, BookRepository $books, Request $request): Response
    {
        $book = $books->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }

        if ($request->isMethod('POST')) {
            $this->UpdateBookStatus->updateBookSatusAndRating($book, $request);
            $this->addFlash('success', 'Książka została zaktualizowana.');

            return $this->redirectToRoute('book_show', ['id' => $id]);

        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'isOwner' => $this->getUser() && $book->getUser() === $this->getUser(),
        ]);
    }

    #[Route('/book/{id}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, BookRepository $books, Request $request, EditBook $editBook): Response
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
            try {
                $editBook->editBook($book, $request);
                return $this->redirectToRoute('app_profile');
            } catch (\InvalidArgumentException $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }
        return $this->render('book/edit.html.twig', [
            'book' => $book,
        ]);
    }
}