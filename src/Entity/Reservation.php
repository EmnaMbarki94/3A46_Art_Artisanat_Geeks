<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le libellé ne doit pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le libellé ne peut pas dépasser 255 caractères.")]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'état ne doit pas être vide.")]
    private ?string $etatE = null;

    #[ORM\ManyToOne]
    private ?Event $relation = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de places ne doit pas être vide.")]
    #[Assert\GreaterThan(0, message: "Le nombre de places doit être supérieur à 0.")]
    private ?int $nb_place = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getEtatE(): ?string
    {
        return $this->etatE;
    }

    public function setEtatE(string $etatE): static
    {
        $this->etatE = $etatE;
        return $this;
    }

    public function getRelation(): ?Event
    {
        return $this->relation;
    }

    public function setRelation(?Event $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    public function getNbPlace(): ?int
    {
        return $this->nb_place;
    }

    public function setNbPlace(?int $nb_place): static
    {
        $this->nb_place = $nb_place;
        return $this;
    }
}
