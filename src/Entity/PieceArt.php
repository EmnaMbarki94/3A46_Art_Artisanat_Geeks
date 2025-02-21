<?php

namespace App\Entity;

use App\Repository\PieceArtRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



#[ORM\Entity(repositoryClass: PieceArtRepository::class)]
class PieceArt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: 'Le nom de la pièce ne peut pas être vide.')]
    private ?string $nomP = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: 'La date de création ne peut pas être vide.')]

    private ?\DateTimeInterface $dateCrea = null;

    #[ORM\Column(length: 255)]
    private ?string $photoP = null;

    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: 'La description ne peut pas être vide.')]
    #[Assert\Length(max: 255, maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères.')]

    private ?string $descP = null;

    #[ORM\ManyToOne(inversedBy: 'pieceArt')]
    private ?Galerie $galerie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomP(): ?string
    {
        return $this->nomP;
    }

    public function setNomP(string $nomP): static
    {
        $this->nomP = $nomP;

        return $this;
    }

    public function getDateCrea(): ?\DateTimeInterface
    {
        return $this->dateCrea;
    }

    public function setDateCrea(\DateTimeInterface $dateCrea): static
    {
        $this->dateCrea = $dateCrea;

        return $this;
    }

    public function getPhotoP(): ?string
    {
        return $this->photoP;
    }

    public function setPhotoP(string $photoP): static
    {
        $this->photoP = $photoP;

        return $this;
    }

    public function getDescP(): ?string
    {
        return $this->descP;
    }

    public function setDescP(string $descP): static
    {
        $this->descP = $descP;

        return $this;
    }

    public function getGalerie(): ?Galerie
    {
        return $this->galerie;
    }

    public function setGalerie(?Galerie $galerie): static
    {
        $this->galerie = $galerie;

        return $this;
    }
}
