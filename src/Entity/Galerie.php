<?php

namespace App\Entity;

use App\Repository\GalerieRepository;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalerieRepository::class)]
class Galerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: "Le nom de la galerie ne peut pas être vide.")]
    private ?string $nomG = null;

    #[ORM\Column(length: 255)]
    private ?string $photoG = null;

    #[ORM\Column(length: 255)]

    #[Assert\NotBlank(message: "La description de la galerie ne peut pas être vide.")]
    private ?string $descG = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le type de galerie ne peut pas être vide.")]
    #[Assert\Regex(
        pattern: '/^[^\d]*$/',
        message: "Le type de galerie ne doit pas contenir de chiffres."
    )]

    private ?string $typeG = null;

    /**
     * @var Collection<int, PieceArt>
     */
    #[ORM\OneToMany(targetEntity: PieceArt::class, mappedBy: 'galerie')]
    private Collection $pieceArt;

    #[ORM\OneToOne(cascade: ['persist'])]
    private ?User $user = null;

    

    public function __construct()
    {
        $this->pieceArt = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomG(): ?string
    {
        return $this->nomG;
    }

    public function setNomG(string $nomG): static
    {
        $this->nomG = $nomG;

        return $this;
    }

    public function getPhotoG(): ?string
    {
        return $this->photoG;
    }

    public function setPhotoG(string $photoG): static
    {
        $this->photoG = $photoG;

        return $this;
    }

    public function getDescG(): ?string
    {
        return $this->descG;
    }

    public function setDescG(string $descG): static
    {
        $this->descG = $descG;

        return $this;
    }

    public function getTypeG(): ?string
    {
        return $this->typeG;
    }

    public function setTypeG(string $typeG): static
    {
        $this->typeG = $typeG;

        return $this;
    }

    /**
     * @return Collection<int, PieceArt>
     */
    public function getPieceArt(): Collection
    {
        return $this->pieceArt;
    }

    public function addPieceArt(PieceArt $pieceArt): static
    {
        if (!$this->pieceArt->contains($pieceArt)) {
            $this->pieceArt->add($pieceArt);
            $pieceArt->setGalerie($this);
        }

        return $this;
    }

    public function removePieceArt(PieceArt $pieceArt): static
    {
        if ($this->pieceArt->removeElement($pieceArt)) {
            // set the owning side to null (unless already changed)
            if ($pieceArt->getGalerie() === $this) {
                $pieceArt->setGalerie(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }


   
}
