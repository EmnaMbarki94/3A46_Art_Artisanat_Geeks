<?php

namespace App\Entity;

use App\Repository\MagasinRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MagasinRepository::class)]
class Magasin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomM = null;

    #[ORM\Column(length: 255)]
    private ?string $typeM = null;

    #[ORM\Column(length: 255)]
    private ?string $photoM = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomM(): ?string
    {
        return $this->nomM;
    }

    public function setNomM(string $nomM): static
    {
        $this->nomM = $nomM;

        return $this;
    }

    public function getTypeM(): ?string
    {
        return $this->typeM;
    }

    public function setTypeM(string $typeM): static
    {
        $this->typeM = $typeM;

        return $this;
    }

    public function getPhotoM(): ?string
    {
        return $this->photoM;
    }

    public function setPhotoM(string $photoM): static
    {
        $this->photoM = $photoM;

        return $this;
    }
}
