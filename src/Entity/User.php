<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
	fields: 'email',
	message: "Un compte est déjà rattaché à cette adresse email",
)]
#[UniqueEntity(
	fields: 'name',
	message: "Ce nom est déjà utilisé")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE ,length: 50, nullable: false)]
    private string|DateTimeImmutable $created_at;

    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $isVerified = false;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: Figure::class)]
                  	private Collection $figures;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenValidator = null;

	#[ORM\Column(type: 'json')]
	private array $roles = [];

    public function __construct()
    {
		$this->figures = new ArrayCollection();
        $this->messages = new ArrayCollection();
  		$this->images = new ArrayCollection();
		$this->created_at = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

	public function getRoles(): array
                  	{
                  		$roles[] = 'ROLE_USER';

                  		return $roles;
                  	}

	public function setRoles(array $roles): self
	{
		$this->roles = $roles;

		return $this;
	}

	public function eraseCredentials()
                  	{

                  	}

	public function getUserIdentifier(): string
                  	{
                  		return (string) $this->name;
                  	}
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified = false): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

	/**
	 * @return Collection|null
	 */
    public function getMessages(): ?Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

	/**
	 * @return Collection<int, Figure>
	 */
	public function getFigures(): Collection
                  	{
                  		return $this->figures;
                  	}

	public function addFigures (Figure $figures): self
                  	{
                  		if (!$this->figures->contains($figures))
                  		{
                  			$this->figures->add($figures);
                  			$figures->setUser($this);
                  		}
                  
                  		return $this;
                  	}

    public function addFigure(Figure $figure): self
    {
        if (!$this->figures->contains($figure)) {
            $this->figures->add($figure);
            $figure->setUser($this);
        }

        return $this;
    }

    public function removeFigure(Figure $figure): self
    {
        if ($this->figures->removeElement($figure)) {
            // set the owning side to null (unless already changed)
            if ($figure->getUser() === $this) {
                $figure->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImage(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setUser($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getUser() === $this) {
                $image->setUser(null);
            }
        }

        return $this;
    }

    public function getTokenValidator(): ?string
    {
        return $this->tokenValidator;
    }

    public function setTokenValidator(?string $tokenValidator): static
    {
        $this->tokenValidator = $tokenValidator;

        return $this;
    }
}
