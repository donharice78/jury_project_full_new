<?php

namespace App\Entity;

use App\Repository\ContactMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactMessageRepository::class)]
class ContactMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Identifiant unique du message de contact

    #[ORM\Column(length: 255)]
    private ?string $name = null; // Nom de l'expéditeur du message

    #[ORM\Column(length: 255)]
    private ?string $email = null; // Email de l'expéditeur du message

    #[ORM\Column]
    private ?string $phone = null; // Numéro de téléphone de l'expéditeur (optionnel)

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null; // Contenu du message de contact

    // Méthode pour obtenir l'identifiant du message de contact
    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode pour obtenir le nom de l'expéditeur
    public function getName(): ?string
    {
        return $this->name;
    }

    // Méthode pour définir le nom de l'expéditeur
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir l'email de l'expéditeur
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Méthode pour définir l'email de l'expéditeur
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir le numéro de téléphone de l'expéditeur
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    // Méthode pour définir le numéro de téléphone de l'expéditeur
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }

    // Méthode pour obtenir le contenu du message de contact
    public function getMessage(): ?string
    {
        return $this->message;
    }

    // Méthode pour définir le contenu du message de contact
    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this; // Retourne l'instance actuelle pour la chaîne de méthodes
    }
}
