<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Identifiant unique du commentaire

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null; // Contenu du commentaire

    /**
     * Relation ManyToOne avec l'entité User
     *
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user = null; // Utilisateur qui a laissé le commentaire

    /**
     * Note attribuée dans le commentaire
     *
     * @ORM\Column(type="integer")
     */
    private ?int $rating = null; // Note associée au commentaire

    // Méthode pour obtenir l'identifiant du commentaire
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir le contenu du commentaire
    public function getContent(): ?string
    {
        return $this->content;
    }

    // Méthode pour définir le contenu du commentaire
    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir l'utilisateur associé au commentaire
    public function getUser(): ?User
    {
        return $this->user;
    }

    // Méthode pour définir l'utilisateur associé au commentaire
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir la note du commentaire
    public function getRating(): ?int
    {
        return $this->rating;
    }

    // Méthode pour définir la note du commentaire
    public function setRating(int $rating): static
    {
        $this->rating = $rating;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }
}
