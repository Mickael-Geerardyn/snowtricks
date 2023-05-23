<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    private ?string $content = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
    private string|DateTimeImmutable $created_at;

	#[ORM\ManyToOne(targetEntity: Figure::class, inversedBy: "messages")]
	#[ORM\JoinColumn(nullable: false)]
	private ?Figure $figure = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): string
	{
        return $this->created_at;
    }

    public function setCreatedAt(): self
    {
        $this->created_at = $this->created_at->format("d-m-Y");

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

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
