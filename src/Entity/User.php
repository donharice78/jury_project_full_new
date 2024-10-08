<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Il y a déjà un compte avec cet email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null; // Identifiant unique de l'utilisateur

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: 'L\'email "{{ value }}" n\'est pas valide.')]
    #[Assert\NotBlank(message: 'L\'email ne doit pas être vide.')]
    private ?string $email = null; // Adresse email de l'utilisateur

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe ne doit pas être vide.')]
    private ?string $password = null; // Mot de passe de l'utilisateur

    private ?string $confirmPassword = null; // Mot de passe de confirmation pour les formulaires (non stocké dans la base de données)

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'Le nom d\'utilisateur ne doit pas être vide.')]
    private ?string $username = null; // Nom d'utilisateur

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne doit pas être vide.')]
    private ?string $firstName = null; // Prénom de l'utilisateur

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom de famille ne doit pas être vide.')]
    private ?string $lastName = null; // Nom de famille de l'utilisateur

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $phone = null; // Numéro de téléphone de l'utilisateur (optionnel)

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $terms = null; // Conditions acceptées par l'utilisateur (optionnel)

    #[ORM\Column(type: Types::JSON)]
    private array $roles = []; // Rôles de l'utilisateur (ex. ROLE_USER, ROLE_ADMIN)

    #[ORM\Column(length: 255)]
    private ?string $photo = null; // Photo de profil de l'utilisateur (chemin du fichier)

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'name')]
    private Collection $comments;

    /**
     * @var Collection<int, Course>
     */
    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'users')]
    private Collection $courses; // Commentaires associés à l'utilisateur

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getTerms(): ?string
    {
        return $this->terms;
    }

    public function setTerms(?string $terms): self
    {
        $this->terms = $terms;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Ajouter le rôle par défaut si non présent

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Effacer les données sensibles temporaires
    }

    public function getUserIdentifier(): string
    {
        return $this->email; // Utilisé pour l'identification de l'utilisateur
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): static
    {
        $this->photo = $photo;
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // Assigner la relation possédante à null (sauf si déjà changé)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->addUser($this); // Only if you have a bidirectional relation
        }

        return $this;
    }

    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function removeCourse(Course $course): static
    {
        $this->courses->removeElement($course);

        return $this;
    }




}
