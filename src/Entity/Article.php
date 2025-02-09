<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomA = null;

    #[ORM\Column]
    private ?float $prixA = null;

    #[ORM\Column(length: 255)]
    private ?string $descA = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $ensemblePhotos = [];

    #[ORM\ManyToOne]
    private ?Magasin $magasin = null;

    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?Commande $commande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomA(): ?string
    {
        return $this->nomA;
    }

    public function setNomA(string $nomA): static
    {
        $this->nomA = $nomA;

        return $this;
    }

    public function getPrixA(): ?float
    {
        return $this->prixA;
    }

    public function setPrixA(float $prixA): static
    {
        $this->prixA = $prixA;

        return $this;
    }

    public function getDescA(): ?string
    {
        return $this->descA;
    }

    public function setDescA(string $descA): static
    {
        $this->descA = $descA;

        return $this;
    }

    public function getEnsemblePhotos(): array
    {
        return $this->ensemblePhotos;
    }

    public function setEnsemblePhotos(array $ensemblePhotos): static
    {
        $this->ensemblePhotos = $ensemblePhotos;

        return $this;
    }

    public function getMagasin(): ?Magasin
    {
        return $this->magasin;
    }

    public function setMagasin(?Magasin $magasin): static
    {
        $this->magasin = $magasin;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }
}
