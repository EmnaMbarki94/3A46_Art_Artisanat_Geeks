<?php

namespace App\Entity;

use App\Repository\MagasinRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MagasinRepository::class)]
class Magasin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: "nom_m", length: 255)] // Correspond au nom de la colonne dans la base de données
    #[Assert\NotBlank(message: "Le nom du magasin est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomM = null;


    #[ORM\Column(type: "string")]
    #[Assert\NotNull(message: "Le type est requis.")]
    #[Assert\Length(min: 3, max: 100, minMessage: "Le type doit avoir au moins {{ limit }} caractères.")]
    private ?string $typeM = null;

    #[ORM\Column(name: "photo_m", length: 255, nullable: true)] // Correspond au nom de la colonne dans la base de données
    // Vous pouvez utiliser Assert\Image ou Assert\File pour valider les images
    #[Assert\Image(
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)."
    )]
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
