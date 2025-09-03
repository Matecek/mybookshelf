<?php


declare(strict_types=1);

namespace App\Services\Home;

use App\Entity\User;
use App\Repository\BookRepository;

class ShowBook
{
    public function __construct(private BookRepository $bookRepository)
    {
    }

    public function getBooksForHomepage(?User $user): array
    {
        if (!$user) {
            return [];
        }

        return $this->bookRepository->findAllBooksForHomepage();
    }
}