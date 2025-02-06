<?php

namespace App\Entity;

use App\Repository\CoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoursRepository::class)]
class Cours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomC = null;

    #[ORM\Column(length: 255)]
    private ?string $categC = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenuC = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateC = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $heureC = null;

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
}
