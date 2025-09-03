<?php

declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\User;
use App\Entity\Book;
use App\Entity\UserBook;
use App\Repository\UserBookRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserBookStatus
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserBookRepository $userBookRepository
    ) {}

    public function setBookStatus(User $user, Book $book, string $status): UserBook
    {
        $userBook = $this->userBookRepository->findByUserAndBook($user, $book);

        if (!$userBook) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
        }

        $userBook->setStatus($status);
        $this->entityManager->persist($userBook);
        $this->entityManager->flush();

        return $userBook;
    }

    public function getBookStatus(User $user, Book $book): ?string
    {
        $userBook = $this->userBookRepository->findByUserAndBook($user, $book);
        return $userBook?->getStatus();
    }

    public function addBookToPlanned(User $user, Book $book): UserBook
    {
        return $this->setBookStatus($user, $book, UserBook::STATUS_PLANNED);
    }

    public function getUserBooksByStatus(User $user, string $status): array
    {
        return $this->userBookRepository->findBooksByUserAndStatus($user, $status);
    }
}