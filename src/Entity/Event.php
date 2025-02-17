<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    private ?string $infoE = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)] // Change en DATETIME pour inclure l'heure
    #[Assert\NotBlank(message: "La date de l'événement ne peut pas être vide.")]
    #[Assert\Type("\DateTimeInterface", message: "Veuillez entrer une date valide.")]
    #[Assert\GreaterThan("today", message: "La date de l'événement doit être dans le futur.")]
    private ?\DateTimeInterface $dateE = null;
    

    #[ORM\Column(length: 255)]
    private ?string $photoE = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix simple est obligatoire.")]
    #[Assert\Positive(message: "Le prix simple doit être un nombre positif.")]
    private ?int $prixS = null;
    
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix VIP est obligatoire.")]
    #[Assert\Positive(message: "Le prix VIP doit être un nombre positif.")]
    private ?int $prixVIP = null;
    
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de tickets est obligatoire.")]
    #[Assert\Positive(message: "Le nombre de tickets doit être un nombre positif.")]
    #[Assert\Type(type: 'integer', message: "Le nombre de tickets doit être un entier.")]
    private ?int $nb_ticket = null;
    


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

    public function getPrixS(): ?int
    {
        return $this->prixS;
    }

    public function setPrixS(int $prixS): static
    {
        $this->prixS = $prixS;

        return $this;
    }

    public function getPrixVIP(): ?int
    {
        return $this->prixVIP;
    }

    public function setPrixVIP(int $prixVIP): static
    {
        $this->prixVIP = $prixVIP;

        return $this;
    }

    public function getNbTicket(): ?int
    {
        return $this->nb_ticket;
    }

    public function setNbTicket(int $nb_ticket): static
    {
        $this->nb_ticket = $nb_ticket;

        return $this;
    }
}
