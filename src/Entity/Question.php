<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La question ne peut pas être vide.")]
    private ?string $contenuQ = null;

    #[ORM\Column(type: Types::ARRAY)]
    #[Assert\All([
        new Assert\NotBlank(message: "Chaque réponse ne doit pas être vide.")
    ])]
    private array $reponses = [];

    #[ORM\ManyToOne(targetEntity: Quiz::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    
    private ?Quiz $quiz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenuQ(): ?string
    {
        return $this->contenuQ;
    }

    public function setContenuQ(string $contenuQ): static
    {
        $this->contenuQ = $contenuQ;

        return $this;
    }

    public function getReponses(): array
    {
        return $this->reponses;
    }

    public function setReponses(array $reponses): static
    {
        $this->reponses = $reponses;

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
