<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Email cannot be blank')]
    #[Assert\Email(message: 'Invalid email format, must be : email@service.com')]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: 'Password cannot be blank')]
    #[Assert\Length(min: 1, max: 10, exactMessage: 'Password must be exactly {{ limit }} characters long')]
    private ?string $password = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: 'firstname cannot be blank')]
    #[Assert\Type(type: 'string', message: 'firstname must be a string')]
    #[Assert\Regex(pattern: '/^[a-zA-Z]+$/', message: 'firstname must contain only letters')]
    private ?string $firstName = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: 'lastname cannot be blank')]
    #[Assert\Type(type: 'string', message: 'lastname must be a string')]
    #[Assert\Regex(pattern: '/^[a-zA-Z]+$/', message: 'lastname must contain only letters')]
    private ?string $lastName = null;

    #[ORM\Column(type: "string", length: 8)]
    #[Assert\NotBlank(message: 'NumTel cannot be blank')]
    #[Assert\Regex(pattern: '/^\d+$/', message: 'NumTel must contain only numbers')]
    #[Assert\Length(min: 8, max: 8, exactMessage: 'NumTel must be exactly composed by {{ limit }} numbers')]

    private ?string $numTel = null;

    #[ORM\Column(type: "text")]
    private ?string $address = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $verificationCode = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isVerified = false;
    
    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }
    
    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;
    
        return $this;
    }
    

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $user_agent = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialite = null;

    #[ORM\Column(nullable: true)]
    private ?int $point = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Garantir que chaque utilisateur a ce rôle
        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstName(): ?string 
    { 
        return $this->firstName; 
    }

    public function setFirstName(string $firstName): self
    { 
        $this->firstName = $firstName; 
        return $this;
    }

    public function getLastName(): ?string 
    { 
        return $this->lastName; 
    }

    public function setLastName(string $lastName): self 
    { 
        $this->lastName = $lastName; 
        return $this;
    }

    public function getNumTel(): ?string 
    { 
        return $this->numTel; 
    }

    public function setNumTel(string $numTel): self 
    { 
        $this->numTel = $numTel; 
        return $this; 
    }

    public function getAddress(): ?string 
    { 
        return $this->address; 
    }

    public function setAddress(string $address): self 
    { 
        $this->address = $address; 
        return $this; 
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): self
    {
        $this->verificationCode = $verificationCode;
        return $this;
    }
 

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // Nettoyage des données sensibles (si nécessaire)
    }

    public function getSalt(): ?string
    {
        return null; // Pas besoin de salage supplémentaire
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): static
    {
        $this->user_agent = $user_agent;
        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): static
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(?int $point): static
    {
        $this->point = $point;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): static
    {
        $this->cin = $cin;

        return $this;
    }
}
