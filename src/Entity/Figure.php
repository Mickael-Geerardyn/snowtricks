<?php

namespace App\Entity;

use App\Repository\FigureRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FigureRepository::class)]
class Figure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

	#[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'figures')]
                  	#[ORM\JoinColumn(nullable: false)]
                  	private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'figures')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupe $groupe = null;

	#[ORM\OneToMany(mappedBy: 'figure', targetEntity: Message::class)]
                  	private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Video::class, orphanRemoval: true)]
    private Collection $videos;

    #[ORM\OneToMany(mappedBy: 'figure', targetEntity: Image::class, orphanRemoval: true)]
    private Collection $images;

    #[ORM\Column(length: 50)]
    private string|DateTimeImmutable $created_at;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $updated_at = null;

	public function __construct()
                           	{
                           		$this->messages = new ArrayCollection();
                           		$this->videos = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getGroupe(): ?Groupe
    {
        return $this->groupe;
    }

    public function setGroupe(?Groupe $groupe): self
    {
        $this->groupe = $groupe;

        return $this;
    }

	/**
	 * @return Collection<int, Message>
	 */
	public function getMessages(): Collection
                           	{
                           		return $this->messages;
                           	}

	public function addMessage(Message $message): self
                           	{
                           		if (!$this->messages->contains($message)) {
                           			$this->messages->add($message);
                           			$message->setFigure($this);
                           		}
                           
                           		return $this;
                           	}

	public function removeMessage(Message $message): self
                           	{
                           		if ($this->messages->removeElement($message)) {
                           			// set the owning side to null (unless already changed)
                           			if ($message->getUser() === $this) {
                           				$message->setFigure(null);
                           			}
                           		}
                           
                           		return $this;
                           	}

    /**
     * @return Collection<int, Video>
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setFigure($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getFigure() === $this) {
                $video->setFigure(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setFigure($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getFigure() === $this) {
                $image->setFigure(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setCreatedAt(): self
    {
        $this->created_at = $this->created_at->format("d-m-Y");

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?string $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
