<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserBookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBookRepository::class)]
#[ORM\Table(name: 'user_book')]
#[ORM\UniqueConstraint(name: 'user_book_unique', columns: ['user_id', 'book_id'])]
class UserBook
{
    public const STATUS_PLANNED = 'planowana';
    public const STATUS_READING = 'czytam';
    public const STATUS_FINISHED = 'przeczytana';
    public const STATUS_ABANDONED = 'porzucona';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Book::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Book $book;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = self::STATUS_PLANNED;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBook(): Book
    {
        return $this->book;
    }

    public function setBook(Book $book): self
    {
        $this->book = $book;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_PLANNED => 'Planowana',
            self::STATUS_READING => 'Czytam',
            self::STATUS_FINISHED => 'Przeczytana',
            self::STATUS_ABANDONED => 'Porzucona',
        ];
    }
}