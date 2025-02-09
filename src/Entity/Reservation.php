<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $prixE = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $etatE = null;

    #[ORM\ManyToOne]
    private ?Event $relation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixE(): ?float
    {
        return $this->prixE;
    }

    public function setPrixE(float $prixE): static
    {
        $this->prixE = $prixE;

        return $this;
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
}
