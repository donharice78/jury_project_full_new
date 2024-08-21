<?php

namespace App\Entity;

use App\Repository\BlogsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogsRepository::class)]
class Blogs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Identifiant unique du blog

    #[ORM\Column(length: 255)]
    private ?string $title = null; // Titre du blog

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null; // Contenu du blog

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null; // Date et heure de création du blog

    /**
     * Collection d'images associées au blog
     *
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(targetEntity: Images::class, mappedBy: 'blogs', orphanRemoval: true, cascade: ['persist'])]
    private Collection $images;

    // Constructeur pour initialiser les valeurs par défaut
    public function __construct()
    {
        $this->images = new ArrayCollection(); // Initialise la collection d'images
        $this->createdAt = new \DateTime(); // Initialise createdAt avec la date et l'heure actuelles
    }

    // Méthode pour obtenir l'identifiant du blog
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir le titre du blog
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Méthode pour définir le titre du blog
    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir le contenu du blog
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Méthode pour définir le contenu du blog
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir la date de création du blog
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    // Méthode pour définir la date de création du blog
    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    /**
     * Méthode pour obtenir la collection d'images associées au blog
     *
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    // Méthode pour ajouter une image à la collection
    public function addImage(Images $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setBlogs($this); // Définit le blog associé à l'image
        }
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour retirer une image de la collection
    public function removeImage(Images $image): static
    {
        if ($this->images->removeElement($image)) {
            // Réinitialise la relation bidirectionnelle si nécessaire
            if ($image->getBlogs() === $this) {
                $image->setBlogs(null);
            }
        }
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }
}
