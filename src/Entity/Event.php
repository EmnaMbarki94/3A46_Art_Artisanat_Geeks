<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $typeE = null;

    #[ORM\Column(length: 255)]
    private ?string $infoE = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateE = null;

    #[ORM\Column(length: 255)]
    private ?string $photoE = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTypeE(): ?string
    {
        return $this->typeE;
    }

    public function setTypeE(string $typeE): static
    {
        $this->typeE = $typeE;

        return $this;
    }

    public function getInfoE(): ?string
    {
        return $this->infoE;
    }

    public function setInfoE(string $infoE): static
    {
        $this->infoE = $infoE;

        return $this;
    }

    public function getDateE(): ?\DateTimeInterface
    {
        return $this->dateE;
    }

    public function setDateE(\DateTimeInterface $dateE): static
    {
        $this->dateE = $dateE;

        return $this;
    }

    public function getPhotoE(): ?string
    {
        return $this->photoE;
    }

    public function setPhotoE(string $photoE): static
    {
        $this->photoE = $photoE;

        return $this;
    }
}
