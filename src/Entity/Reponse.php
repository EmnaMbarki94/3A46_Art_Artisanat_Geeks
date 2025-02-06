<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $descRep = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateRep = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Reclamation $reclamation = null;

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
}
