<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Identifiant unique de l'image

    #[ORM\Column(length: 255)]
    private ?string $name = null; // Nom de l'image (chemin ou nom du fichier)

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Blogs $blogs = null; // Blog associé à l'image

    public function getId(): ?int
    {
        return $this->id; // Retourne l'identifiant de l'image
    }

    public function getName(): ?string
    {
        return $this->name; // Retourne le nom de l'image
    }

    public function setName(string $name): static
    {
        $this->name = $name; // Définit le nom de l'image
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getBlogs(): ?Blogs
    {
        return $this->blogs; // Retourne le blog associé à l'image
    }

    public function setBlogs(?Blogs $blogs): static
    {
        $this->blogs = $blogs; // Définit le blog associé à l'image
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }
}
