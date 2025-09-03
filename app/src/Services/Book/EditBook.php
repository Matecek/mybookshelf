<?php
declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class EditBook{
    public function __construct(private EntityManagerInterface $em){}

    public function editBook(Book $book, Request $request): void
    {
        $title       = (string) $request->request->get('title', '');
        $author      = (string) $request->request->get('author', '');
        $description = $request->request->get('description') ?: null;
        $status      = (string) $request->request->get('status', 'reading');

        if ($title === '' || $author === '') {
            throw new \InvalidArgumentException('Tytuł i autor są wymagane.');
        }

        $book->setTitle($title);
        $book->setAuthor($author);
        if (method_exists($book, 'setDescription')) {
            $book->setDescription($description);
        }
        $book->setStatus($status);

        $this->em->flush();
    }
}
