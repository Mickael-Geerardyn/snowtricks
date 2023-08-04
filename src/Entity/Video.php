<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $path = null;

    #[ORM\Column(type: TYPES::DATE_IMMUTABLE, length: 50)]
    private string|DateTimeImmutable $created_at;

    #[ORM\ManyToOne(cascade: null, inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Figure $figure = null;

    #[ORM\ManyToOne(cascade: null, inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

	public function __construct()
         	{
         		$this->created_at = new DateTimeImmutable();
         	}
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at->format("d-m-Y");
    }

    public function setCreatedAt(): self
    {
        return $this;
    }

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;

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
