<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descR = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateR = null;

    #[ORM\Column(length: 255)]
    private ?string $statusR = 'En attente';

    #[ORM\Column(length: 255)]
    private ?string $typeR = null;
    
    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescR(): ?string
    {
        return $this->descR;
    }

    public function setDescR(string $descR): static
    {
        $this->descR = $descR;

        return $this;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->dateR;
    }

    public function setDateR(\DateTimeInterface $dateR): static
    {
        $this->dateR = $dateR;

        return $this;
    }

    public function getStatusR(): ?string
    {
        return $this->statusR;
    }

    public function setStatusR(string $statusR): static
    {
        $this->statusR = $statusR;

        return $this;
    }

    public function getTypeR(): ?string
    {
        return $this->typeR;
    }

    public function setTypeR(string $typeR): static
    {
        $this->typeR = $typeR;

        return $this;
    }
    public function __toString(): string
{
    return (string) $this->id; // Ou un autre champ pertinent, comme $this->titre
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

}
