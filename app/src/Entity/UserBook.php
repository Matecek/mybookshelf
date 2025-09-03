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
    public const STATUS_PLANNED = 'planned';
    public const STATUS_READING = 'reading';
    public const STATUS_FINISHED = 'finished';

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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rating = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters and setters
    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): self
    {
        // Rating tylko dla przeczytanych książek
        if ($this->status === self::STATUS_FINISHED && $rating >= 1 && $rating <= 10) {
            $this->rating = $rating;
        } elseif ($this->status !== self::STATUS_FINISHED) {
            $this->rating = null;
        }

        $this->updatedAt = new \DateTime();
        return $this;
    }

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
            self::STATUS_READING => 'Czytana',
            self::STATUS_FINISHED => 'Przeczytana',
        ];
    }
}