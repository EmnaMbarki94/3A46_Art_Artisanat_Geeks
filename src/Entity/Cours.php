<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre du cours ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomC = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La categorie du cours ne peut pas être vide.")]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: "La categorie doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La categorie ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $categC = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu du cours est obligatoire.")]
    #[Assert\Length(
        min: 10,
        minMessage: "Le contenu du cours doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $contenuC = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateC = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureC = null;

    #[ORM\ManyToOne(inversedBy: 'cours')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $image = "default2.png";

    #[ORM\OneToOne(targetEntity: Quiz::class ,cascade: ['persist', 'remove'])]
    private ?Quiz $quiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomC(): ?string
    {
        return $this->nomC;
    }

    public function setNomC(string $nomC): static
    {
        $this->nomC = $nomC;

        return $this;
    }

    public function getCategC(): ?string
    {
        return $this->categC;
    }

    public function setCategC(string $categC): static
    {
        $this->categC = $categC;

        return $this;
    }

    public function getContenuC(): ?string
    {
        return $this->contenuC;
    }

    public function setContenuC(string $contenuC): static
    {
        $this->contenuC = $contenuC;

        return $this;
    }

    public function getDateC(): ?\DateTimeInterface
    {
        return $this->dateC;
    }

    public function setDateC(\DateTimeInterface $dateC): static
    {
        $this->dateC = $dateC;

        return $this;
    }

    public function getHeureC(): ?\DateTimeInterface
    {
        return $this->heureC;
    }

    public function setHeureC(\DateTimeInterface $heureC): static
    {
        $this->heureC = $heureC;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getQuiz(): ?Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(?Quiz $quiz): static
    {
        $this->quiz = $quiz;

        return $this;
    }
}
