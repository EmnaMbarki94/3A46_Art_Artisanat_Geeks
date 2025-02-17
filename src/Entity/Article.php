<?php
namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de l'article est requis.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom ne doit pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomA = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix est requis.")]
    #[Assert\Type(type: "float", message: "Le prix doit être un nombre.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]
    private ?float $prixA = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description est requise.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private ?string $descA = null;

    #[ORM\Column(length: 255)]    
    private ?string $imagePath = null;


    
    #[ORM\ManyToOne(inversedBy: 'article')]
    private ?Commande $commande = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull(message: "Veuillez sélectionner un magasin.")]
    private ?Magasin $magasin = null;

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

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        if ($imagePath !== null) { // Ne met à jour que si une nouvelle image est envoyée
            $this->imagePath = $imagePath;
        }
    
        return $this;
    }

    public function getMagasin(): ?Magasin
    {
        return $this->magasin;
    }

    public function setMagasin(?Magasin $magasin): self
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
