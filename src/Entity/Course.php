<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Identifiant unique du cours

    #[ORM\Column(length: 255)]
    private ?string $title = null; // Titre du cours

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null; // Description détaillée du cours

    #[ORM\Column(length: 255)]
    private ?string $duration = null; // Durée du cours

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $start_date = null; // Date de début du cours

    #[ORM\Column(length: 255)]
    private ?string $course_format = null; // Format du cours (en ligne, présentiel, etc.)

    #[ORM\Column(length: 255)]
    private ?string $prerequisities = null; // Prérequis nécessaires pour suivre le cours

    #[ORM\Column]
    private ?int $course_fee = null; // Frais du cours

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'courses')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    } // Nom du fichier image associé au cours

    public function getId(): ?int
    {
        return $this->id; // Retourne l'identifiant du cours
    }

    public function getTitle(): ?string
    {
        return $this->title; // Retourne le titre du cours
    }

    public function setTitle(string $title): static
    {
        $this->title = $title; // Définit le titre du cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getDescription(): ?string
    {
        return $this->description; // Retourne la description du cours
    }

    public function setDescription(string $description): static
    {
        $this->description = $description; // Définit la description du cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getDuration(): ?string
    {
        return $this->duration; // Retourne la durée du cours
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration; // Définit la durée du cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date; // Retourne la date de début du cours
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date; // Définit la date de début du cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getCourseFormat(): ?string
    {
        return $this->course_format; // Retourne le format du cours
    }

    public function setCourseFormat(string $course_format): static
    {
        $this->course_format = $course_format; // Définit le format du cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getPrerequisities(): ?string
    {
        return $this->prerequisities; // Retourne les prérequis nécessaires pour le cours
    }

    public function setPrerequisities(string $prerequisities): static
    {
        $this->prerequisities = $prerequisities; // Définit les prérequis pour le cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getCourseFee(): ?int
    {
        return $this->course_fee; // Retourne les frais du cours
    }

    public function setCourseFee(int $course_fee): static
    {
        $this->course_fee = $course_fee; // Définit les frais du cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    public function getImage(): ?string
    {
        return $this->image; // Retourne le nom du fichier image associé au cours
    }

    public function setImage(string $image): static
    {
        $this->image = $image; // Définit le nom du fichier image associé au cours
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addCourse($this); // Make sure to keep it bidirectional
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeCourse($this);
        }

        return $this;
    }
}

