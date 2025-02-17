<?php

namespace App\Entity;

use App\Repository\QuizRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuizRepository::class)]
class Quiz
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La question ne peut pas être vide.")]
    private ?string $titreC = null;

    #[ORM\OneToOne(targetEntity: Cours::class,inversedBy:'quiz')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull(message:"tu doit choisir le cours associé")]
    private ?Cours $cours = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitreC(): ?string
    {
        return $this->titreC;
    }

    public function setTitreC(string $titreC): static
    {
        $this->titreC = $titreC;

        return $this;
    }

    public function getCours(): ?Cours
    {
        return $this->cours;
    }

    public function setCours(?Cours $cours): static
    {
        $this->cours = $cours;

        return $this;
    }
}
