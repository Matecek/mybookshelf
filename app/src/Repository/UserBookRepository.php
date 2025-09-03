<?php


declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\Book;
use App\Entity\UserBook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserBookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserBook::class);
    }

    public function findByUserAndBook(User $user, Book $book): ?UserBook
    {
        return $this->findOneBy(['user' => $user, 'book' => $book]);
    }

    public function findBooksByUserAndStatus(User $user, string $status): array
    {
        return $this->createQueryBuilder('ub')
            ->select('ub', 'b')
            ->join('ub.book', 'b')
            ->where('ub.user = :user')
            ->andWhere('ub.status = :status')
            ->setParameter('user', $user)
            ->setParameter('status', $status)
            ->orderBy('ub.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findBooksByUser(User $user): array
    {
        return $this->createQueryBuilder('ub')
            ->select('ub', 'b')
            ->join('ub.book', 'b')
            ->where('ub.user = :user')
            ->setParameter('user', $user)
            ->orderBy('ub.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}