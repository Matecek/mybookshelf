<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $review = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: UserBook::class, mappedBy: 'book')]
    private Collection $userBooks;

    public function __construct()
    {
        $this->userBooks = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUserBooks(): Collection
    {
        return $this->userBooks;
    }

    public function getAverageRating(): ?float
    {
        $ratings = [];
        foreach ($this->userBooks as $userBook) {
            if ($userBook->getRating() !== null) {
                $ratings[] = $userBook->getRating();
            }
        }

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }

    public function getRatingsCount(): int
    {
        $count = 0;
        foreach ($this->userBooks as $userBook) {
            if ($userBook->getRating() !== null) {
                $count++;
            }
        }
        return $count;
    }

// Metoda do sprawdzania statusu dla konkretnego użytkownika
    public function getStatusForUser(?User $user): ?string
    {
        if (!$user) {
            return null;
        }

        foreach ($this->userBooks as $userBook) {
            if ($userBook->getUser() === $user) {
                return $userBook->getStatus();
            }
        }
        return null;
    }

// Metoda do sprawdzania oceny dla konkretnego użytkownika
    public function getRatingForUser(?User $user): ?int
    {
        if (!$user) {
            return null;
        }

        foreach ($this->userBooks as $userBook) {
            if ($userBook->getUser() === $user) {
                return $userBook->getRating();
            }
        }
        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }


    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): static
    {
        $this->review = $review;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
