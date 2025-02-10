<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
<<<<<<< HEAD
=======

>>>>>>> 67ede58c13395a5f2206417896f7661b70eb0dd6

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'événement ne peut pas être vide.")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom doit comporter au moins 3 caractères.", maxMessage: "Le nom ne peut pas dépasser 255 caractères.")]
    #[Assert\Regex(pattern: "/^[a-zA-ZéèàêôïùÉÈÀÊÔÏÙ]+$/", message: "Le nom ne doit contenir que des lettres.")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type d'événement ne peut pas être vide.")]
    #[Assert\Regex(pattern: "/^[a-zA-ZéèàêôïùÉÈÀÊÔÏÙ]+$/", message: "Le type ne doit contenir que des lettres.")]
    private ?string $typeE = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Les informations sur l'événement ne peuvent pas être vides.")]
    #[Assert\Length(min: 10, max: 255, minMessage: "Les informations doivent comporter au moins 10 caractères.", maxMessage: "Les informations ne peuvent pas dépasser 255 caractères.")]
<<<<<<< HEAD
=======

>>>>>>> 67ede58c13395a5f2206417896f7661b70eb0dd6
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
