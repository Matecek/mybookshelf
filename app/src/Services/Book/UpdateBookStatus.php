<?php

declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\Book;
use App\Entity\UserBook;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UpdateBookStatus
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function updateBookSatusAndRating(Book $book, Request $request): void
    {
        $rating = (int) $request->request->get('rating', 0);
        $status = (string) $request->request->get('status', 'planned');
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        $allowedStatuses = ['planned', 'reading', 'finished'];
        if (!in_array($status, $allowedStatuses, true)) {
            return;
        }

        $userBook = $this->em->getRepository(UserBook::class)
            ->findOneBy(['user' => $user, 'book' => $book]);

        if (!$userBook) {
            $userBook = new UserBook();
            $userBook->setUser($user);
            $userBook->setBook($book);
        }

        $userBook->setStatus($status);

        if ($status === 'finished' && $rating >= 1 && $rating <= 10) {
            $userBook->setRating($rating);
        } elseif ($status !== 'finished') {
            $userBook->setRating(null);
        }

        $this->em->persist($userBook);
        $this->updateBookAverageRating($book);
        $this->em->flush();
    }

    private function updateBookAverageRating(Book $book): void
    {
        $ratings = [];
        foreach ($book->getUserBooks() as $userBook) {
            if ($userBook->getRating() !== null) {
                $ratings[] = $userBook->getRating();
            }
        }

        $averageRating = empty($ratings) ? null : round(array_sum($ratings) / count($ratings), 1);
        $book->setAverageRating($averageRating);
        $this->em->persist($book);
    }
}