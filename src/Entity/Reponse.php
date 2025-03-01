<?php

// src/Entity/Reponse.php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Champ de description de la réponse
    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est requise.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $descRep = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateRep = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Reclamation $reclamation = null;

    // // Nouveau champ rating
    // #[ORM\Column(type: Types::INTEGER, nullable: true)]
    // #[Assert\Range(
    //     min: 1,
    //     max: 5,
    //     minMessage: "La note ne peut être inférieure à {{ limit }}.",
    //     maxMessage: "La note ne peut être supérieure à {{ limit }}."
    // )]
    // private ?int $rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescRep(): ?string
    {
        return $this->descRep;
    }

    public function setDescRep(string $descRep): static
    {
        $this->descRep = $descRep;

        return $this;
    }

    public function getDateRep(): ?\DateTimeInterface
    {
        return $this->dateRep;
    }

    public function setDateRep(\DateTimeInterface $dateRep): static
    {
        $this->dateRep = $dateRep;

        return $this;
    }

    public function getReclamation(): ?Reclamation
    {
        return $this->reclamation;
    }

    public function setReclamation(?Reclamation $reclamation): static
    {
        $this->reclamation = $reclamation;

        return $this;
    }

    // // Méthodes pour accéder et modifier le rating
    // public function getRating(): ?int
    // {
    //     return $this->rating;
    // }

    // public function setRating(?int $rating): static
    // {
    //     $this->rating = $rating;

    //     return $this;
    // }
}

