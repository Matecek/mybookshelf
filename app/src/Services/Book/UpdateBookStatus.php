<?php

declare(strict_types=1);

namespace App\Services\Book;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class UpdateBookStatus
{
    public function __construct(private EntityManagerInterface $em) {}

    public function updateBookSatusAndRating(Book $book, Request $request): void
    {
        $rating = (int) $request->request->get('rating', 0);
        $status = (string) $request->request->get('status', 'reading');

        $allowedStatuses = ['planned', 'reading', 'finished'];
        if ($status !== null && in_array($status, $allowedStatuses, true)) {
            $book->setStatus($status);
        }

        if ($book->getStatus() !== 'finished') {
            $book->setRating(null);
        } else {
            if ($rating > 0 && $rating >= 1 && $rating <= 10) {
                $book->setRating($rating);
            }
        }

        $this->em->flush();
    }
}